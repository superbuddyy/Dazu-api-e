<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Enums\CompanyType;
use App\Enums\OfferStatus;
use App\Events\Offer\OfferCreated;
use App\Events\Offer\OfferUpdated;
use App\Events\Offer\PaidOfferCreated;
use App\Events\User\UserCreated;
use App\Exceptions\UserExists;
use App\Http\Requests\Offer\OfferStoreRequest;
use App\Http\Requests\Offer\ReportOfferRequest;
use App\Http\Requests\Offer\ShowOfferRequest;
use App\Http\Resources\PreviewOffer\PreviewOfferCollection;
use App\Http\Resources\PreviewOffer\PreviewOfferExtendedResource;
use App\Jobs\SendEmailJob;
use App\Laravue\Acl;
use App\Mail\Offer\ReportOffer;
use App\Managers\CompanyManager;
use App\Managers\PreviewOfferManager;
use App\Managers\TransactionManager;
use App\Managers\UserManager;
use App\Managers\UserProfileManager;
use App\Models\Offer;
use App\Models\PreviewOffer;
use App\Models\Subscription;
use App\Models\User;
use App\Payments\PayPal\Checkout;
use App\Services\GoogleAnalytics;
use App\Services\ImageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PreviewOfferController
{
    /**
     * @var OfferManager
     */
    private PreviewOfferManager $offerManager;

    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;

    /**
     * @var Checkout
     */
    private Checkout $paypalCheckout;

    /** @var UserProfileManager */
    private UserProfileManager $userProfileManager;

    /**
     * OfferManager constructor.
     * @param OfferManager $offerManager
     * @param UserManager $userManager
     * @param UserProfileManager $userProfileManager
     * @param Checkout $paypalCheckout
     * @param TransactionManager $transactionManager
     * @param ImageService $imageService
     */
    public function __construct(
        PreviewOfferManager $offerManager,
        UserManager $userManager,
        UserProfileManager $userProfileManager,
        Checkout $paypalCheckout,
        TransactionManager $transactionManager,
        ImageService $imageService

    )
    {
        $this->offerManager = $offerManager;
        $this->userManager = $userManager;
        $this->userProfileManager = $userProfileManager;
        $this->paypalCheckout = $paypalCheckout;
        $this->transactionManager = $transactionManager;
        $this->imageService = $imageService;
    }

    public function index(): Response
    {
        $offers = $this->offerManager->getList(OfferStatus::ACTIVE, true, true);
        return response()->success(new OfferCollection($offers));
    }

    public function show(PreviewOffer $offer): Response
    {
        return response()->success(new PreviewOfferExtendedResource($offer));
    }

    /**
     * @param OfferStoreRequest $request
     * @return mixed
     */
    public function store(OfferStoreRequest $request): Response
    {
        DB::beginTransaction();

        try {
            $user = Auth::user() ?: null;

            $offer = $this->offerManager->store(
                $request->get('title'),
                $request->get('description'),
                (int)$request->get('price'),
                $request->get('category'),
                $request->get('attributes'),
                $request->get('lat'),
                $request->get('lon'),
                $request->get('location_name'),
                $request->get('links', []),
                $request->get('visible_from_date'),
                $user ? $user->id : null,
                $request->get('has_raise_one') == 'true' ? true : false,
                $request->get('has_raise_three') == 'true' ? true : false,
                $request->get('has_raise_ten') == 'true' ? true : false,
                $request->get('is_urgent') == 'true' ? true : false,
                $request->get('is_bargain') == 'true' ? true : false,
            );

            $offerToken = null;
            if ($request->get('preview')) {
                $offerToken = (string)Str::uuid();
                Cache::put('offer-token:' . $offer->id, $offerToken, Carbon::now()->addHour());
            }

            // if ($request->has('subscription')) {
            //     $subscription = Subscription::findOrFail($request->subscription);
            //     $offer->subscriptions()->detach();
            //     $offer->subscriptions()
            //         ->attach(
            //             $subscription->id,
            //             ['end_date' => Carbon::now()->addHours($subscription->duration)]
            //         );
            // }

            if ($request->has('images')) {
                $position = 1;
                foreach ($request->file('images') as $file) {
                    $this->offerManager->storeImage($file, $offer, $position,'photo');
                    $position++;
                }
            }
            if ($request->has('projectPlans')) {
                $position = 1;
                foreach ($request->file('projectPlans') as $file) {
                    $this->offerManager->storeImage($file, $offer, $position,'project_plan');
                    $position++;
                }
            }
            if ($request->has('avatar') && $request->avatar !== null){
                $this->offerManager->storeAvatar($offer, $request->file('avatar'));
            }

            if (
                in_array($request->type, [CompanyType::DEVELOPER, CompanyType::AGENCY])
                && $request->has('video_avatar')
                && $request->video_avatar !== null
            ) {
                $this->offerManager->storeVideoAvatar($offer, $request->get('video_avatar'));
            }
            DB::commit();
            return response()->success(new PreviewOfferExtendedResource($offer, $offerToken), Response::HTTP_CREATED);
        } catch (UserExists $e) {
            DB::rollBack();
            return response()->errorWithLog(['error' => 'email_already_exist'], Response::HTTP_BAD_REQUEST, ['message' => $e]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->errorWithLog($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, ['message' => $e]);
        }
    }

    /**
     * @param Request $request
     * @param Offer $offer
     * @return mixed
     */
    public function update(Request $request, PreviewOffer $offer): Response
    {

        DB::beginTransaction();
        try {
            $offer = $this->offerManager->update(
                $offer,
                $request->get('title'),
                $request->get('description'),
                (int)$request->get('price'),
                $request->get('category'),
                $request->get('attributes'),
                $request->get('lat'),
                $request->get('lon'),
                $request->get('location_name'),
                $request->get('links', []),
                $request->get('visible_from_date', null)
            );

            if ($offer === null) {
                DB::rollBack();
                return response()->errorWithLog(
                    'Fail to update offer',
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    ['offer_id' => $offer->id]
                );
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->errorWithLog($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, ['message' => $e]);
        }

        if ($request->has('images')) {
            $photos = $offer->photos;
            foreach ($photos as $photo) {
                if ($photo->img_type == 'photo') {
                    $this->offerManager->removeImage($photo->id, $photo->file['path_name']);    
                }
            }
            $position = 1;
            foreach ($request->file('images') as $file) {
                $this->offerManager->storeImage($file, $offer, $position,'photo');
                $position++;
            }
        }
        if ($request->has('projectPlans')) {
            $photos = $offer->photos;
            foreach ($photos as $photo) {
                if ($photo->img_type == 'project_plan') {
                    $this->offerManager->removeImage($photo->id, $photo->file['path_name']);    
                }
            }
            $position = 1;
            foreach ($request->file('projectPlans') as $file) {
                $this->offerManager->storeImage($file, $offer, $position,'project_plan');
                $position++;
            }
        }
        if ($request->has('avatar') && $request->avatar !== null && $request->avatar !== 'undefined'){
            $this->offerManager->storeAvatar($offer, $request->file('avatar'));
        }

        if (
            in_array($request->type, [CompanyType::DEVELOPER, CompanyType::AGENCY])
            && $request->has('video_avatar')
            && $request->video_avatar !== null
            && $request->video_avatar !== 'undefined'
        ) {
            $this->offerManager->storeVideoAvatar($offer, $request->get('video_avatar'));
        }
        DB::commit();
        return response()->success(new PreviewOfferExtendedResource($offer), Response::HTTP_OK);
    }

    public function changeStatus(Request $request, Offer $offer): Response
    {
        if ($offer->user_id !== Auth::id()) {
            return response()->error('', Response::HTTP_FORBIDDEN);
        }

        try {
            $this->offerManager->changeStatus($offer, $request->get('status'));
            return response()->success('', Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return response()->errorWithLog($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, ['message' => $e]);
        }
    }

    public function activate(Request $request): Response
    {
        try {
            $this->offerManager->changeStatusMultiple($request->get('offers'), OfferStatus::ACTIVE);
            return response()->success('', Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return response()->errorWithLog($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, ['message' => $e]);
        }
    }

    public function deactivate(Request $request): Response
    {
        try {
            $this->offerManager->changeStatusMultiple($request->get('offers'), OfferStatus::IN_ACTIVE_BY_USER);
            $offers = Auth::user()->offers()
                ->orderBy('expire_time', 'DESC')
                ->orderBy('status', 'ASC')
                ->paginate(10);
            return response()->success(OfferCollection::make($offers), Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->errorWithLog($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, ['message' => $e]);
        }
    }

    public function refresh(Offer $offer)
    {
        $offerSubscription = $offer->activeSubscription;
        if ($offer->refresh_count >= $offerSubscription->number_of_refreshes) {
            $ref = 'offer::' . $offer->id . 'user::' . Auth::id();
            $result = $this->paypalCheckout->createOrder($ref, $offerSubscription->refresh_price);
            if ($result === false || $result->statusCode !== Response::HTTP_CREATED) {
                return response()->errorWithLog(
                    'failed to create order',
                    ['user_id' => Auth::id(), Response::HTTP_UNPROCESSABLE_ENTITY, 'offer_id' => $offer->id]
                );
            }

            $transaction = $this->transactionManager->store(
                [
                    [
                        'description' => 'Odświeżenie ogłoszenia',
                        'price' => $offerSubscription->refresh_price,
                        'qty' => 1,
                        'unit' => 'szt.',
                    ]
                ],
                $offer->id,
                $offer->title
            );

            Redis::set(
                $result->result->id,
                json_encode([
                    'context' => 'offer-refresh',
                    'user_id' => Auth::id(),
                    'offer_id' => $offer->id,
                    'transaction_id' => $transaction->id,
                ]),
                'EX',
                '120'
            );

            return response()->success($result->result);
        }

        $this->offerManager->refresh($offer);
        $offers = $this->offerManager->getMyList();

        return response()->success(OfferCollection::make($offers), Response::HTTP_OK);
    }

    public function calculateBill(Offer $offer): Response
    {
        if ($offer->status === OfferStatus::ACTIVE) {
            return response()->success('Nothing to pay', Response::HTTP_NOT_FOUND);
        }

        return response()->success(
            ['offerTitle' => $offer->title, 'bill' => $offer->calculateBill()],
            Response::HTTP_OK
        );
    }

    public function getSimilar(Offer $offer)
    {
        $offers = $this->offerManager->getSimilar($offer);
        return response()->success($offers);
    }

    public function report(ReportOfferRequest $request, Offer $offer)
    {
        dispatch(new SendEmailJob(new ReportOffer($offer, $request->get('message'))));

        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function charge(Request $request, Offer $offer)
    {
        $userId = Auth::id() ?? $offer->user->id;
        DB::beginTransaction();
        try {
            $additionalAmount = 0;
            if ($request->has('invoice_data')) {
                $this->userProfileManager->update($request->invoice_data, $offer->user);
            }

            if ($request->has('subscription')) {
                $subscription = Subscription::findOrFail($request->subscription);
                $offer->subscriptions()
                    ->attach(
                        $subscription->id,
                        ['end_date' => Carbon::now()->addHours($subscription->duration)]
                    );
                $additionalAmount += $subscription->price;
            }

            $ref = 'offer::' . $offer->id . 'user::' . $userId;
            $bill = $offer->calculateBill();
            $result = $this->paypalCheckout->createOrder($ref, $bill['billAmount'] + $additionalAmount);
            if ($result === false || $result->statusCode !== Response::HTTP_CREATED) {
                return response()->errorWithLog(
                    'failed to create order',
                    ['user_id' => $userId, 'offer_id' => $offer->id]
                );
            }

            $lineItems = [];
            foreach ($bill['details'] as $item) {
                $lineItems[] = [
                    'description' => $item['name'],
                    'price' => $item['value'],
                    'qty' => 1,
                    'unit' => 'szt.',
                    'id' => $item['id'] ?? null,
                ];
            }

            $transaction = $this->transactionManager->store(
                $lineItems,
                $offer->id,
                $offer->title,
                $offer->user,
                $request->has('invoice_data')
            );


            Redis::set(
                $result->result->id,
                json_encode([
                    'context' => 'offer',
                    'user_id' => $userId,
                    'offer_id' => $offer->id,
                    'transaction_id' => $transaction->id ?? null,
                ]),
                'EX',
                '120'
            );
            DB::commit();
            return response()->success($result->result);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(
                'Error occured on payment callback',
                [
                    'user_id' => $userId,
                    'offer_id' => $offer->id,
                    'error_msg' => $e->getMessage(),
                ]
            );
            return response()->success(config('dazu.frontend_url') . '?payment-status=fail');
        }
    }

    public function getStats(Offer $offer)
    {
        if ($offer->user->id !== Auth::id()) {
            return response()->error('', Response::HTTP_FORBIDDEN);
        }

        if ($offer->created_at->startOfDay()->format('Y-m-d') === Carbon::now()->startOfDay()->format('Y-m-d')) {
            return response()->success([
                'labels' => [$offer->created_at->format('Y-m-d')],
                'data' => [0]
            ]);
        }

        $analytics = new GoogleAnalytics();

        $cacheStats = Redis::get('stats:' . $offer->slug);
        if (!$cacheStats) {
            [$labels, $data] = $analytics->getPageViews(
                $offer->created_at->format('Y-m-d'),
                Carbon::now()->subDay()->format('Y-m-d'),
                '/ogloszenia/' . $offer->slug
            );


            Redis::set(
                'stats:' . $offer->slug,
                json_encode([
                    'labels' => $labels,
                    'data' => $data
                ]),
                'EX',
                Carbon::now()->secondsUntilEndOfDay()
            );
        } else {
            $arrayCacheStats = json_decode($cacheStats, true);
            $labels = $arrayCacheStats['labels'];
            $data = $arrayCacheStats['data'];
        }

        return response()->success([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    public function raise(Request $request, Offer $offer): Response
    {
        if ($request->get('reduce_raise')) {
            $this->offerManager->reduceRaise($offer);
            $this->offerManager->raise($offer);
            $offers = Auth::user()->offers()
                ->orderBy('expire_time', 'DESC')
                ->orderBy('status', 'ASC')
                ->paginate(10);

            return response()->success(OfferCollection::make($offers), Response::HTTP_OK);
        }
        $offerSubscription = $offer->activeSubscription;
        if ($offer->raise_count >= $offerSubscription->number_of_raises) {
            $ref = 'offer::' . $offer->id . 'user::' . Auth::id();
            $result = $this->paypalCheckout->createOrder($ref, $offerSubscription->raise_price);
            if ($result === false || $result->statusCode !== Response::HTTP_CREATED) {
                return response()->errorWithLog(
                    'failed to create order',
                    ['user_id' => Auth::id(), Response::HTTP_UNPROCESSABLE_ENTITY, 'offer_id' => $offer->id]
                );
            }

            $transaction = $this->transactionManager->store(
                [
                    [
                        'description' => 'Podbicie ogłoszenia',
                        'price' => $offerSubscription->raise_price,
                        'qty' => 1,
                        'unit' => 'szt.',
                    ]
                ],
                $offer->id,
                $offer->title
            );


            Redis::set(
                $result->result->id,
                json_encode([
                    'context' => 'offer-raise',
                    'user_id' => Auth::id(),
                    'offer_id' => $offer->id,
                    'transaction_id' => $transaction->id,
                ]),
                'EX',
                '120'
            );

            return response()->success($result->result);
        }

        $this->offerManager->raise($offer);
        $offers = Auth::user()->offers()
            ->orderBy('expire_time', 'DESC')
            ->orderBy('status', 'ASC')
            ->paginate(10);

        return response()->success(OfferCollection::make($offers), Response::HTTP_OK);
    }
}

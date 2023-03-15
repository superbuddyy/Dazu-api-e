<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Enums\OfferStatus;
use App\Events\Offer\OfferUpdated;
use App\Http\Controllers\Controller;
use App\Http\Resources\Offer\OfferCollection;
use App\Http\Resources\Offer\OfferExtendedResource;
use App\Managers\OfferManager;
use App\Models\Offer;
use App\Models\Subscription;
use App\Services\SearchService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    /**
     * @var OfferManager
     */
    protected $offerManager;
    /**
     * @var SearchService
     */
    private $searchService;

    public function __construct(
        OfferManager $offerManager,
        SearchService $searchService
    )
    {
        $this->offerManager = $offerManager;
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        $offers = $this->searchService->search(
            $request->except(['page', 'order_by','order']),
            false,
            $request->get('order_by') ?? 'price',
            $request->get('order') ?? 'DESC'
            // $request->get('filter') ?? 'all'
        );

        return response()->success(new OfferCollection($offers, true));
    }

    public function show(Offer $offer)
    {
        return response()->success(new OfferExtendedResource($offer));
    }

    public function update(Request $request, Offer $offer)
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
                $request->get('visible_from_date', null),
                $request->get('status')
            );

            if ($offer === null) {
                DB::rollBack();
                return response()->errorWithLog(
                    'Fail to update offer',
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    ['offer_id' => $offer->id]
                );
            }

            if ($request->has('subscription')) {
                $subscription = Subscription::findOrFail($request->subscription);
                $this->offerManager->changeSubscription($offer, $subscription);
            }

            if ($request->has('images')) {
                $photos = $offer->photos;
                foreach ($photos as $photo) {
                    $this->offerManager->removeImage($photo->id, $photo->file['path_name']);
                }
                $position = 1;
                foreach ($request->file('images') as $file) {
                    $this->offerManager->storeImage($file, $offer, $position);
                    $position++;
                }
            }

            event(new OfferUpdated($offer));
            DB::commit();
            return response()->success(new OfferExtendedResource($offer), Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->errorWithLog($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, ['message' => $e]);
        }
    }

    public function changeStatus(Request $request, Offer $offer): JsonResponse
    {
        try {
            if ($request->has('subscription')) {
                $subscription = Subscription::findOrFail($request->get('subscription'));
                $this->offerManager->changeSubscription($offer, $subscription);
            }
            if ($request->has('status')){
                if ($offer->status === OfferStatus::EXPIRED && $offer->expire_time <= Carbon::now()) {
                    $offer->expire_time = Carbon::now()->addHours(config('dazu.offer.expire_time'));
                    $offer->save();
                }
                $this->offerManager->changeStatus($offer, $request->get('status'), $request->get('note'));
                
            }
            return response()->success('', Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->errorWithLog($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, ['message' => $e]);
        }
    }

    public function changeSubscription(Request $request): JsonResponse
    {
        $offer = Offer::findOrFail($request->offer_id);
        $subscription = Offer::findOrFail($request->subscription_id);
        $this->offerManager->changeSubscription($offer, $subscription);
        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function destroy(Request $request, Offer $offer)
    {
        try {
            $photos = $offer->photos;
            foreach ($photos as $photo) {
                $this->offerManager->removeImage($photo->id, $photo->file['path_name']);
            }
            echo $offer->id;
            $del = Offer::findOrFail($offer->id)->delete();
            return response()->success($del, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->errorWithLog($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY, ['message' => $e]);
        }
    }
}

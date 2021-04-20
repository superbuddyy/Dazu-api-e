<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Enums\AvatarType;
use App\Events\User\AgentCreated;
use App\Http\Requests\User\AgentStoreRequest;
use App\Http\Requests\User\UserPhoneRequest;
use App\Http\Resources\Notification\NotificationCollection;
use App\Http\Resources\Offer\OfferCollection;
use App\Http\Resources\User\AgentCollection;
use App\Http\Resources\User\AgentResource;
use App\Http\Resources\User\ProfilePageResource;
use App\Laravue\Acl;
use App\Managers\CompanyManager;
use App\Managers\NotificationManager;
use App\Managers\TransactionManager;
use App\Managers\UserManager;
use App\Models\Notification;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Payments\PayPal\Checkout;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UserController
{
    /** @var UserManager */
    protected $userManager;

    /** @var NotificationManager */
    protected $notificationManager;

    /** @var CompanyManager */
    protected $companyManager;

    /**
     * UserController constructor.
     * @param UserManager $userManager
     * @param CompanyManager $companyManager
     * @param NotificationManager $notificationManager
     */
    public function __construct(
        UserManager $userManager,
        CompanyManager $companyManager,
        NotificationManager $notificationManager
    ) {
        $this->userManager = $userManager;
        $this->companyManager = $companyManager;
        $this->notificationManager = $notificationManager;
    }

    public function storeAgent(AgentStoreRequest $request)
    {
        $user = $this->userManager->storeAgent(
            $request->get('email'),
            $request->get('name')
        );

        $user->company_id = Auth::user()->company->id;
        $user->save();

        event(new AgentCreated($user));

        return response()->success(new AgentResource($user), Response::HTTP_CREATED);
    }

    public function getAgents()
    {
        $users = $this->userManager->getUserCompanyAgents();
        return response()->success(new AgentCollection($users), Response::HTTP_OK);
    }

    public function deleteAgent(User $user)
    {
        if ($user->company->id !== Auth::user()->company->id) {
            return response()->success('', Response::HTTP_FORBIDDEN);
        }
        $this->userManager->destroy($user);
        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function getMyOffers()
    {
        $user = Auth::user();
        if ($user->getRoleName() === Acl::ROLE_COMPANY && $user->company) {
            $companyMembers = User::where('company_id', $user->company_id)->pluck('id')->all();
            $offers = Offer::whereIn('user_id', $companyMembers)->paginate(10);
        } else {
            $offers = $user->offers()
                ->orderBy('expire_time', 'DESC')
                ->orderBy('status', 'ASC')
                ->paginate(10);
        }
        return response()->success(OfferCollection::make($offers));
    }

    public function getMyNotifications()
    {
        $notifications = Notification::where('user_id', Auth::id())->where('active', true)->paginate(10);
        return response()->success(new NotificationCollection($notifications));
    }

    public function deactivateNotifications()
    {
        $this->notificationManager->deactivate();
        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function delete()
    {
        $this->userManager->destroy();
        Auth::user()->offers()->delete();
        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function storeAvatar(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $avatarType = $request->get('avatar_type', AvatarType::PHOTO);

        DB::beginTransaction();
        try {
            $ref = 'user::' . $user->id . 'avatar';
            $price = Setting::where('name', "avatar_$avatarType.price")->firstOrFail()['value'];

            $result = resolve(Checkout::class)->createOrder($ref, (int)$price);
            if ($result === false || $result->statusCode !== Response::HTTP_CREATED) {
                return response()->errorWithLog(
                    'failed to create order',
                    ['user_id' => $user->id]
                );
            }

            $desc = $avatarType === AvatarType::PHOTO ? 'Avatar' : 'Wideo Avatar';
            $transaction = resolve(TransactionManager::class)->store(
                [
                    [
                        'description' => $desc,
                        'unit' => 'szt.',
                        'price' => $price,
                        'qty' => 1,
                    ]
                ],
                null,
                $desc
            );

            if ($request->has('avatar') && $request->avatar !== null) {
                if ($avatarType === AvatarType::PHOTO){
                    $avatar = $request->file('avatar');
                    $this->userManager->storeAvatar($user, $avatar);
                }

                if ($avatarType === AvatarType::VIDEO_URL){
                    $this->userManager->storeVideoAvatar($user, $request->get('avatar'));
                }
            }

            Redis::set(
                $result->result->id,
                json_encode([
                    'context' => "avatar_$avatarType",
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id
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
                    'user_id' => Auth::id(),
                    'error_msg' => $e->getMessage(),
                ]
            );
            return redirect()->away(env('FRONT_URL') . '?payment-status=fail');
        }
    }

    public function getPhone(UserPhoneRequest $request, User $user)
    {
        return response()->success(['phone' => $user->profile->phone ?? '']);
    }

    public function showProfile(User $user)
    {
        return response()->success(new ProfilePageResource($user));
    }
}

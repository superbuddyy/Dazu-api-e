<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Enums\AvatarType;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Managers\OfferManager;
use App\Managers\SubscriptionManager;
use App\Managers\TransactionManager;
use App\Managers\UserManager;
use App\Models\Transaction;
use App\Payments\Checkout;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /** @var TransactionManager */
    private $transactionManager;

    /** @var OfferManager */
    private $offerManager;

    /** @var SubscriptionManager */
    private $subscriptionManager;

    /** @var UserManager */
    private $userManager;

    public function __construct(
        TransactionManager $transactionManager,
        OfferManager $offerManager,
        SubscriptionManager $subscriptionManager,
        UserManager $userManager
    ) {
        $this->transactionManager = $transactionManager;
        $this->offerManager = $offerManager;
        $this->subscriptionManager = $subscriptionManager;
        $this->userManager = $userManager;
    }

    public function callback(Request $request)
    {
        $checkout = new Checkout($request->get('gateway', 'paypal'));
        DB::beginTransaction();
        try {
            $token = $request->get($checkout->getCallbackIdentifierField());
            Log::info($token);
            if ($request->get('gateway') === $checkout::PAYPAL_SLUG) {
                $result = $checkout->callbackAction($token);
                if ($result === false) {
                    return response()->errorWithLog(
                        'failed to execute order',
                        ['user_id' => Auth::id(), 'request_token' => $token]
                    );
                }
            }

            $cachedInfo = Redis::get($token);
            if ($cachedInfo === null) {
                Log::error('Invalid token');
                return response()->success('Invalid token');
            }

            $cachedInfoArray = json_decode($cachedInfo, true);

            $transaction = Transaction::findOrFail($cachedInfoArray['transaction_id']);
            $this->transactionManager->updateStatus($transaction, TransactionStatus::PAID);

            switch ($cachedInfoArray['context']) {
                case 'offer':
                    $this->offerManager->handlePaymentCallback($cachedInfoArray);
                    break;
                case 'offer-refresh':
                    $this->offerManager->handleRefreshPaymentCallback($cachedInfoArray);
                    break;
                case 'offer-raise':
                    $this->offerManager->handleRaisePaymentCallback($cachedInfoArray);
                    break;
                case 'subscription':
                    $this->subscriptionManager->handlePaymentCallback($cachedInfoArray);
                    break;
                case 'avatar_' . AvatarType::PHOTO:
                case 'avatar_' . AvatarType::VIDEO_URL:
                    $avatar = $this->userManager->handlePaymentCallback($cachedInfoArray);
                    DB::commit();

                if ($request->get('gateway') === $checkout::PAYPAL_SLUG) {
                    return redirect()->away(
                        config('dazu.frontend_url')
                        . '/ustawienia-konta/?payment-status=success&'.$cachedInfoArray['context'].'=' . $avatar->file['url']
                    );
                } else {
                    return response()->success([]);
                }

                default:
                    return response()->error('Invalid context', Response::HTTP_BAD_REQUEST);
            }

            DB::commit();
            if ($request->get('gateway') === $checkout::PAYPAL_SLUG) {
                return redirect()->away(config('dazu.frontend_url') . '?payment-status=success');
            } else {
                return response()->success([]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(
                'Error occured on payment callback',
                [
                    'user_id' => $cachedInfoArray['user_id'],
                    'model_id' => $cachedInfoArray['subscription_id'] ?? $cachedInfoArray['offer_id'] ?? null,
                    'error_msg' => $e,
                ]
            );
            return redirect()->away(config('dazu.frontend_url') . '?payment-status=fail');
        }
    }
}

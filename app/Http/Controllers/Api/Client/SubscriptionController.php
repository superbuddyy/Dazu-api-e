<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Enums\TransactionStatus;
use App\Managers\TransactionManager;
use App\Models\Offer;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Payments\PayPal\Checkout;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SubscriptionController
{
    /** @var Checkout  */
    private $paypalCheckout;

    /** @var TransactionManager */
    private $transactionManager;

    public function __construct(Checkout $paypalCheckout, TransactionManager $transactionManager)
    {
        $this->paypalCheckout = $paypalCheckout;
        $this->transactionManager = $transactionManager;
    }

    public function index()
    {
        return response()->success(Subscription::all());
    }

    public function buy(Request $request, Subscription $subscription, Offer $offer)
    {
        DB::beginTransaction();
        try {
            $ref = 'subscription::' . $subscription->id . 'offer::' . $offer->id;
            $result = $this->paypalCheckout->createOrder($ref, $subscription->price);
            if ($result === false || $result->statusCode !== Response::HTTP_CREATED) {
                return response()->errorWithLog(
                    'failed to create order',
                    ['offer_id' => $offer->id, 'subscription_id' => $subscription->id]
                );
            }

            $transaction = $this->transactionManager->store(
                [
                    [
                        'description' => $subscription->name,
                        'unit' => 'szt.',
                        'price' => $subscription->price,
                        'qty' => 1,
                    ]
                ],
                $offer->id,
                $subscription->name
            );

            Redis::set(
                $result->result->id,
                json_encode([
                    'context' => 'subscription',
                    'user_id' => Auth::id(),
                    'offer_id' => $offer->id,
                    'subscription_id' => $subscription->id,
                    'transaction_id' => $transaction->id,
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
                    'offer_id' => $offer->id,
                    'subscription_id' => $subscription->id,
                    'error_msg' => $e->getMessage(),
                ]
            );
            return response()->error(
                ['url' => config('dazu.frontend_url') . '?payment-status=fail'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}

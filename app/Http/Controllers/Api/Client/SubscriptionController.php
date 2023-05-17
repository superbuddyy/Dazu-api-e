<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Managers\TransactionManager;
use App\Models\Offer;
use App\Models\Subscription;
use App\Payments\Checkout;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SubscriptionController
{
    /** @var TransactionManager */
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
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
            $inputData = $request->get('subscriptions') ?? null;
            $ref = 'subscription::' . $subscription->id . 'offer::' . $offer->id;
            $price = $subscription->price;
            $lineItems = [];
            $lineItems[] = [
                'description' => $subscription->name,
                'unit' => 'szt.',
                'price' => $subscription->price,
                'qty' => 1,
            ];
            if (isset($inputData['is_bargain']) && !empty($inputData['is_bargain'])) {
                $price = $price + $subscription->bargain_price;
                $lineItems[] = [
                    'description' => 'Okazja',
                    'unit' => 'szt.',
                    'price' => $subscription->bargain_price,
                    'qty' => 1,
                ];
            }
            if (isset($inputData['is_urgent']) && !empty($inputData['is_urgent'])) {
                $price = $price + $subscription->urgent_price;
                $lineItems[] = [
                    'description' => 'Pilne',
                    'unit' => 'szt.',
                    'price' => $subscription->urgent_price,
                    'qty' => 1,
                ];
            }
            if (isset($inputData['has_raise_one']) && !empty($inputData['has_raise_one'])) {
                $price = $price + $subscription->raise_price;
                $lineItems[] = [
                    'description' => 'Podibicie',
                    'unit' => 'szt.',
                    'price' => $subscription->raise_price,
                    'qty' => 1,
                ];
            }
            if (isset($inputData['has_raise_three']) && !empty($inputData['has_raise_three'])) {
                $price = $price + $subscription->raise_price_three;
                $lineItems[] = [
                    'description' => 'Podibicie x3',
                    'unit' => 'szt.',
                    'price' => $subscription->raise_price_three,
                    'qty' => 1,
                ];
            }
            if (isset($inputData['has_raise_ten']) && !empty($inputData['has_raise_ten'])) {
                $price = $price + $subscription->raise_price_ten;
                $lineItems[] = [
                    'description' => 'Podibicie x10',
                    'unit' => 'szt.',
                    'price' => $subscription->raise_price_ten,
                    'qty' => 1,
                ];
            }
            $checkout = new Checkout($request->get('gateway', Checkout::TPAY_SLUG));

            $platform = $request->get('platform') ?? 'desktop';
            var_dump($platform);
            die;
            $result = $checkout->createOrder($ref, $price, $platform);
            if ($result === false) {
                return response()->errorWithLog(
                    'failed to create order',
                    ['offer_id' => $offer->id, 'subscription_id' => $subscription->id]
                );
            }

            $transaction = $this->transactionManager->store(
                $lineItems,
                $offer->id,
                $subscription->name
            );

            Redis::set(
                $checkout->extractId($result),
                json_encode([
                    'context' => 'subscription',
                    'user_id' => Auth::id(),
                    'offer_id' => $offer->id,
                    'subscription_id' => $subscription->id,
                    'transaction_id' => $transaction->id,
                    'subscriptions' => $inputData
                ]),
                'EX',
                '120'
            );
            DB::commit();
            return response()->success($checkout->extractUrl($result));
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

<?php

declare(strict_types=1);

namespace App\Payments\PayPal;

use Illuminate\Support\Facades\Log;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;
use PayPalHttp\HttpResponse;

class Checkout
{
    /**
     * @var \PayPalCheckoutSdk\Core\SandboxEnvironment
     */
    private $environment;
    /**
     * @var \PayPalCheckoutSdk\Core\PayPalHttpClient
     */
    private $client;

    public function __construct()
    {
        $this->environment = new SandboxEnvironment(
            config('payments.paypal.client_id'),
            config('payments.paypal.client_secret')
        );
        $this->client = new PayPalHttpClient($this->environment);
    }

    /**
     * @param string $refId
     * @param int $amount
     * @param string $currency
     * @return false|HttpResponse
     */
    public function createOrder(string $refId, int $amount, string $currency = 'PLN')
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $refId, // PaymentId
                'amount' => [
                    'value' => $amount / 100,
                    'currency_code' => $currency
                ]
            ]],
            'application_context' => [
                'cancel_url' => config('payments.paypal.cancel_url'),
                'return_url' => config('payments.paypal.return_url')
            ]
        ];

        try {
            // Call API with your client and get a response for your call
            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            return $this->client->execute($request);
        } catch (HttpException $ex) {
            Log::error('Fail to create paypal order', [$ex->getMessage()]);
            return false;
        }
    }

    /**
     * @param string $orderId
     * @return false|HttpResponse
     */
    public function execute(string $orderId)
    {
        $request = new OrdersCaptureRequest($orderId);
        $request->prefer('return=representation');
        try {
            // Call API with your client and get a response for your call
            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            return $this->client->execute($request);
        } catch (HttpException $ex) {
            Log::error('Fail to execute paypal order', [$ex->getMessage()]);
            return false;
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Payments\Tpay;

use Exception;
use Illuminate\Support\Facades\Log;
use tpayLibs\src\_class_tpay\TransactionApi;
use tpayLibs\src\_class_tpay\Utilities\TException;

class Checkout extends TransactionApi
{
    public function __construct()
    {
        $this->merchantSecret = 'demo';
        $this->merchantId = 1010;
        $this->trApiKey = '75f86137a6635df826e3efe2e66f7c9a946fdde1';
        $this->trApiPass = 'p@$$w0rd#@!';
        parent::__construct();
    }

    public function createOrder(string $refId, int $amount)
    {
        $config = array(
            'amount' => $amount / 100,  // Divide by 100, because we keep amounts in int.
            'description' => 'Opłata dazu.pl',
            'crc' => $refId,
            'result_url' => config('app.url') . '/api/payments/callback?gateway=tpay',
            'result_email' => config('dazu.company_info.email'),
            'return_url' => config('dazu.frontend_url') . '?payment-status=success',
            'email' => 'artur.jurkiewiczpyl@gmail.com',
            'name' => 'John Doe',
            'group' => 150,
            'accept_tos' => 1,
        );
        try {
            return $this->create($config);
        } catch (TException $e) {
            Log::error('Fail to create paypal order', [$e->getMessage()]);
            return false;
        }
    }

    public function extractId(array $result)
    {
        if (!isset($result['title'])) {
            throw new Exception('Fail to extract id from Tpay response');
        }

        return $result['title'];
    }

    public function extractUrl(array $result)
    {
        if (!isset($result['url'])) {
            throw new Exception('Fail to extract url from Tpay response');
        }

        return $result['url'];
    }

    public function callbackAction(string $token)
    {
    }
}

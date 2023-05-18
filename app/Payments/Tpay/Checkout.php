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
        $this->merchantSecret = '&=91+tZ$M)P2q56NM60Tg|eSIWUk$';
        $this->merchantId = 81752;
        $this->trApiKey = 'caf7d3e00267a028f04a9b859b9d84dd0da1337f';
        $this->trApiPass = '81752-3522dfb5d6dfd4f2';
        parent::__construct();
    }

    public function createOrder(string $refId, int $amount)
    {
        $splitRefId = explode(':', $refId); 
        $real_refId = $splitRefId[0];
        $platform = $splitRefId[1];

        $config = array(
            'amount' => $amount / 100,  // Divide by 100, because we keep amounts in int.
            'description' => 'Opłata dazu.pl',
            'crc' => $real_refId,
            'result_url' => 'https://dazu.pl/api/payments/callback?gateway=tpay',
            'result_email' => config('dazu.company_info.email'),
            'return_url' => $platform == 'desktop' ? config('dazu.frontend_url') . '?payment-status=success' : 'm' . config('dazu.frontend_url') . '?payment-status=success',
            'email' => 'dazunieruchomosci@gmail.com',
            'name' => 'John Doe',
            'group' => 150,
            'accept_tos' => 1,
        );

//        dd($config);

        try {
            return $this->create($config);
        } catch (TException $e) {
            Log::error('Fail to create paypal order', [$e->getMessage()]);
            throw $e;
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

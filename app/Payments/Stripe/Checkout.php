<?php

declare(strict_types=1);

namespace App\Payments\Stripe;

use Exception;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;

class Checkout extends Order
{
    public function __construct()
    {
        parent::__construct();
        $this->stripeApikey = 'sk_test_51NBJfMEspc22iNrVHc413A46yj1WIclmmdsFYgAOLp7kvIMJuKRJxVDZMUQNNwl1ZbTaRz03EtRCdF54TzUEv3J100q6KW2rbP';
        Stripe::setApiKey($this->stripeApikey);
    }

    public function createOrder(string $refId, int $amount)
    {
        $splitRefId = explode('/', $refId); 
        $real_refId = $splitRefId[0];
        $platform = $splitRefId[1];

        $basic_url = '';
        if($platform === 'desktop')
            $basic_url = config('dazu.frontend_url');
        else if($platform === 'mobile')
            $basic_url = config('dazu.mobile_frontend_url');

        $config = array(
            'amount' => $amount / 100,  // Divide by 100, because we keep amounts in int.
            'description' => 'Fee dazu.pl',
            'crc' => $real_refId,
            'result_url' => 'https://dazu.pl/api/payments/callback?gateway=stripe',
            'result_email' => config('dazu.company_info.email'),
            'return_url' => $basic_url . '?payment-status=success',
            'return_error_url' => $basic_url . '?payment-status=fail',
            'email' => 'dazunieruchomosci@gmail.com',
            'name' => 'John Doe',
            'group' => 150,
            'accept_tos' => 1,
        );

//        dd($config);

        try {
            return Charge::create($config);
        } catch (TException $e) {
            Log::error('Fail to create stripe order', [$e->getMessage()]);
            throw $e;
        }
    }

    public function extractId(array $result)
    {
        if (!isset($result['title'])) {
            throw new Exception('Fail to extract id from Stripe response');
        }

        return $result['title'];
    }

    public function extractUrl(array $result)
    {
        if (!isset($result['url'])) {
            throw new Exception('Fail to extract url from Stripe response');
        }

        return $result['url'];
    }

    public function callbackAction(string $token)
    {
    }
}


<?php

declare(strict_types=1);

namespace App\Payments;


class Checkout
{
    public const PAYPAL_SLUG = 'paypal';
    public const TPAY_SLUG = 'tpay';
    public const STRIPE_SLUG = 'stripe';

    private $gateway;

    public function __construct(string $gateway)
    {
        // init gateway
        if ($gateway == self::TPAY_SLUG) {
            $this->gateway = new \App\Payments\Tpay\Checkout();
        } else if ($gateway == self::PAYPAL_SLUG) {
            $this->gateway = new \App\Payments\PayPal\Checkout();
        } else if ($gateway == self::STRIPE_SLUG) {
            $this->gateway = new \App\Payments\Stripe\Checkout();
        }
    }

    public function createOrder(string $refId, int $amount)
    {
        return $this->gateway->createOrder($refId, $amount);
    }

    public function extractId($result)
    {
        return $this->gateway->extractId($result);
    }

    public function extractUrl($result)
    {
        return $this->gateway->extractUrl($result);
    }

    public function getCallbackIdentifierField()
    {
        return 'tr_id';
    }

    public function callbackAction(string $token)
    {
        return $this->gateway->callbackAction($token);
    }
}

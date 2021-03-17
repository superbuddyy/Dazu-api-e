<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;


use App\Managers\SubscriptionManager;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController
{
    /** @var SubscriptionManager */
    private $subscriptionManager;

    public function __construct(SubscriptionManager $subscriptionManager)
    {
        $this->subscriptionManager = $subscriptionManager;
    }

    public function index(): Response
    {
        return response()->success(Subscription::whereIn('id', [2, 3, 4])->get());
    }

    public function show(subscription $subscription): Response
    {
        return response()->success($subscription);
    }

    public function update(Request $request, Subscription $subscription): Response
    {
        $subscription = $this->subscriptionManager->update($subscription, $request->all());
        return response()->success($subscription);
    }
}

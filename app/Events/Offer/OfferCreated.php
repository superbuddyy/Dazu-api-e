<?php

declare(strict_types=1);

namespace App\Events\Offer;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class OfferCreated
{
    use SerializesModels;

    /**
     * @var Offer
     */
    public $offer;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param Offer $offer
     * @param User $user
     */
    public function __construct(Offer $offer, User $user)
    {
        $this->offer = $offer;
        $this->user = $user;
    }
}

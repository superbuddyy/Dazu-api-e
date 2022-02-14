<?php

declare(strict_types=1);

namespace App\Events\Offer;

use App\Models\Offer;
use Illuminate\Queue\SerializesModels;

class OfferUpdated
{
    use SerializesModels;

    /**
     * @var Offer
     */
    public $offer;

    /**
     * Create a new event instance.
     *
     * @param Offer $offer
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }
}

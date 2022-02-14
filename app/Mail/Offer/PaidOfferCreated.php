<?php

declare(strict_types=1);

namespace App\Mail\Offer;

use App\Laravue\Models\User;
use App\Mail\BaseMail;
use App\Models\Offer;

class PaidOfferCreated extends BaseMail
{
    /** @var Offer $offer */
    public $offer;

    public function __construct(Offer $offer)
    {
        parent::__construct();
        $this->offer = $offer;
    }

    public function build(): self
    {
        $this->to($this->offer->user->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.paid_offer_created'));
        return $this->markdown('mail.offer.paid_offer_created');
    }
}

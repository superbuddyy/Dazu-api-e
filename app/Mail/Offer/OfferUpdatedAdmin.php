<?php

declare(strict_types=1);

namespace App\Mail\Offer;

use App\Laravue\Models\User;
use App\Mail\BaseMail;
use App\Models\Offer;

class OfferUpdatedAdmin extends BaseMail
{
    /** @var Offer $offer */
    public $offer;

    /** @var User $user */
    public $user;

    public function __construct(Offer $offer, User $user)
    {
        parent::__construct();
        $this->offer = $offer;
    }

    public function build(): self
    {
        $this->to(config('dazu.admin.email'))
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.offer_updated_admin'));
        return $this->markdown('mail.offer.updated_admin');
    }
}

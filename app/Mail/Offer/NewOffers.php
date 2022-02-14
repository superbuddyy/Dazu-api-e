<?php

declare(strict_types=1);

namespace App\Mail\Offer;

use App\Laravue\Models\User;
use App\Mail\BaseMail;
use App\Models\Offer;
use Illuminate\Support\Collection;

class NewOffers extends BaseMail
{
    /** @var Collection $offers */
    public $offers;

    /** @var User $user */
    public $user;

    public function __construct(Collection $offers, User $user)
    {
        parent::__construct();
        $this->offers = $offers;
        $this->user = $user;
    }

    public function build(): self
    {
        $this->to($this->user->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.new_offers'));
        return $this->markdown('mail.offer.new_offers');
    }
}

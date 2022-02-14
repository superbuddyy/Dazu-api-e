<?php

declare(strict_types=1);

namespace App\Mail\Offer;

use App\Laravue\Models\User;
use App\Mail\BaseMail;
use App\Models\Offer;

class ReportOffer extends BaseMail
{
    /** @var Offer $offer */
    public $offer;

    /** @var string */
    public $message;

    public function __construct(Offer $offer, string $message)
    {
        parent::__construct();
        $this->offer = $offer;
        $this->message = $message;
    }

    public function build(): self
    {
        $this->to(config('dazu.admin.email'))
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.offer_reported') . ' - #' . $this->offer->id);
        return $this->markdown('mail.offer.reported');
    }
}

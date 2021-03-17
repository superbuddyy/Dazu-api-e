<?php

declare(strict_types=1);

namespace App\Mail\Contact;

use App\Models\Offer as OfferModel;
use App\Mail\BaseMail;

class Offer extends BaseMail
{
    /** @var string */
    public $email;

    /** @var string */
    public $offer;

    /** @var string */
    public $message;

    /** @var bool */
    public $wantToSee;

    /** @var string */
    public $name;

    /**
     * EmailConfirmation constructor.
     * @param string $email
     * @param string $name
     * @param string $message
     * @param bool $wantToSee
     * @param OfferModel $offer
     */
    public function __construct(
        string $email,
        string $name,
        string $message,
        bool $wantToSee,
        OfferModel $offer
    ) {
        parent::__construct();
        $this->email = $email;
        $this->name = $name;
        $this->message = $message;
        $this->wantToSee = $wantToSee;
        $this->offer = $offer;
    }

    public function build(): self
    {
        $this->to($this->offer->user->email)
            ->replyTo($this->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.contact_offer'));
        return $this->markdown('mail.contact.offer');
    }
}

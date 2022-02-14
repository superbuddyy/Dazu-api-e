<?php

declare(strict_types=1);

namespace App\Mail\Contact;

use App\Models\Offer as OfferModel;
use App\Mail\BaseMail;

class ContactForm extends BaseMail
{
    /** @var string */
    public $email;

    /** @var string */
    public $message;

    /** @var string */
    public $topic;

    /** @var string */
    public $name;

    /**
     * EmailConfirmation constructor.
     * @param string $email
     * @param string $name
     * @param string $message
     * @param string $topic
     */
    public function __construct(
        string $email,
        string $name,
        string $message,
        string $topic
    ) {
        parent::__construct();
        $this->email = $email;
        $this->name = $name;
        $this->message = $message;
        $this->topic = $topic;
    }

    public function build(): self
    {
        $this->to(config('dazu.admin.email'))
            ->replyTo($this->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.contact_form'));
        return $this->markdown('mail.contact.contact_form');
    }
}

<?php

declare(strict_types=1);

namespace App\Mail\Contact;

use App\Models\ContactEmails;
use App\Mail\BaseMail;

class ContactConfirmation extends BaseMail
{
    /** @var string */
    public $user;
    public $email;
    public $url;
    public function __construct(
        string $email,
        ContactEmails $user,
        string $url
    ) {
        parent::__construct();
        $this->email = $email;
        $this->user = $user;
        $this->url = $url;
    }

    public function build(): self
    {
        $this->to($this->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.contact_form'));
        return $this->markdown('mail.contact.contact_confirmation
            ');
    }
}

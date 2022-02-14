<?php

declare(strict_types=1);

namespace App\Mail\Contact;

use App\Models\Offer as OfferModel;
use App\Mail\BaseMail;
use App\Models\User;

class UserProfileContact extends BaseMail
{
    /** @var string */
    public $email;

    /** @var string */
    public $message;

    /** @var User */
    public $user;

    /** @var string */
    public $name;

    /**
     * EmailConfirmation constructor.
     * @param string $email
     * @param string $name
     * @param string $message
     * @param User $user
     */
    public function __construct(
        string $email,
        string $name,
        string $message,
        User $user
    ) {
        parent::__construct();
        $this->email = $email;
        $this->name = $name;
        $this->message = $message;
        $this->user = $user;
    }

    public function build(): self
    {
        $this->to($this->user->email)
            ->replyTo($this->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.contact_form'));
        return $this->markdown('mail.contact.profile');
    }
}

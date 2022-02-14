<?php

declare(strict_types=1);

namespace App\Mail\User;

use App\Mail\BaseMail;
use App\Models\User;

class NewsletterActivated extends BaseMail
{
    /** @var User $user */
    public $user;

    /** @var string|null */
    public $email;

    /**
     * EmailConfirmation constructor.
     * @param User|null $user
     * @param string|null $email
     */
    public function __construct(User $user = null, string $email = null)
    {
        parent::__construct();
        $this->user = $user;
        $this->email = $email;
    }

    public function build(): self
    {
        $email = $this->user->email ?? $this->email;
        $this->to($email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.newsletter_activated'));
        return $this->markdown('mail.user.newsletter_activated');
    }
}

<?php

declare(strict_types=1);

namespace App\Mail\User;

use App\Mail\BaseMail;
use App\Models\User;

class UserDeleted extends BaseMail
{
    /** @var string $email */
    public $email;

    /**
     * @param User|null $user
     */
    public function __construct(string $email)
    {
        parent::__construct();
        $this->email = $email;
    }

    public function build(): self
    {
        $email = $this->email;
        $this->to($email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.user_deleted'));
        return $this->markdown('mail.user.user_deleted');
    }
}

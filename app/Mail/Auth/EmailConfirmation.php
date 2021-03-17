<?php

declare(strict_types=1);

namespace App\Mail\Auth;

use App\Laravue\Models\User;
use App\Mail\BaseMail;

class EmailConfirmation extends BaseMail
{
    /** @var User $user */
    public $user;

    /**
     * EmailConfirmation constructor.
     * @param $user
     */
    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    public function build(): self
    {
        $this->to($this->user->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.registration'));
        return $this->markdown('mail.user.register');
    }
}

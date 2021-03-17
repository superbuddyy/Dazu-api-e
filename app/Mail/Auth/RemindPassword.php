<?php

declare(strict_types=1);

namespace App\Mail\Auth;

use App\Laravue\Models\User;
use App\Mail\BaseMail;

class RemindPassword extends BaseMail
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
            ->subject(trans('mail.remind_password'));
        return $this->markdown('mail.user.remind_password');
    }
}

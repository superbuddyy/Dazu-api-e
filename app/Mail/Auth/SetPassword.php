<?php

declare(strict_types=1);

namespace App\Mail\Auth;

use App\Laravue\Models\User;
use App\Mail\BaseMail;

class SetPassword extends BaseMail
{
    /** @var User $user */
    public $user;

    public const AGENT = 'agent';
    private ?string $variant;

    /**
     * EmailConfirmation constructor.
     * @param User $user
     * @param string|null $variant
     */
    public function __construct(User $user, ?string $variant = null)
    {
        parent::__construct();
        $this->user = $user;
        $this->variant = $variant;
    }

    public function build(): self
    {
        $this->to($this->user->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject($this->variant === null ? trans('mail.set_password') : trans('mail.agent_created'));
        return $this->markdown('mail.user.set_password');
    }
}

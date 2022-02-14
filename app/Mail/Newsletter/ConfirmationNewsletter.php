<?php

declare(strict_types=1);

namespace App\Mail\Newsletter;

use App\Laravue\Models\User;
use App\Mail\BaseMail;

class ConfirmationNewsletter extends BaseMail
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    public $token;

    /**
     * EmailConfirmation constructor.
     * @param string $email
     * @param string $token
     */
    public function __construct(string $email, string $token)
    {
        parent::__construct();
        $this->email = $email;
        $this->token = $token;
    }

    public function build(): self
    {
        $this->to($this->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject(trans('mail.newsletter_confirmation'));
        return $this->markdown('mail.user.newsletter_confirmation');
    }
}

<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class NewsletterDeactivated
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}

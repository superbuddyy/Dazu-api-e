<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\User\AgentCreated;
use App\Events\User\NewsletterActivated;
use App\Events\User\UserCreated;
use App\Jobs\SendEmailJob;
use App\Mail\Auth\EmailConfirmation;
use App\Mail\User\NewsletterActivated as NewsletterActivatedMail;
use App\Mail\Auth\SetPassword;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;

class UserEventSubscriber
{
    /**
     * @param UserCreated $event
     */
    public function onUserCreated(UserCreated $event): void
    {
        $event->user->verification_token = Str::uuid()->toString();
        $event->user->save();

        dispatch(new SendEmailJob(new EmailConfirmation($event->user)));
    }

    /**
     * @param AgentCreated $event
     */
    public function onAgentCreated(AgentCreated $event): void
    {
        $event->user->verification_token = Str::uuid()->toString();
        $event->user->save();

        dispatch(new SendEmailJob(new SetPassword($event->user, SetPassword::AGENT)));
    }

    public function onNewsletterActivated(NewsletterActivated $event)
    {
        dispatch(new SendEmailJob(new NewsletterActivatedMail($event->user)));
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            UserCreated::class,
            self::class . '@' . 'onUserCreated'
        );

        $events->listen(
            AgentCreated::class,
            self::class . '@' . 'onAgentCreated'
        );

        $events->listen(
            NewsletterActivated::class,
            self::class . '@' . 'onNewsletterActivated'
        );
    }
}

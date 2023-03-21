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
use Mail;

class UserEventSubscriber
{
    /**
     * @param UserCreated $event
     */
    public function onUserCreated(UserCreated $event): void
    {
        $event->user->verification_token = Str::uuid()->toString();
        $event->user->save();

        // dispatch(new SendEmailJob(new EmailConfirmation($event->user)));

        $link = 'https://dazu.pl/dokoncz-rejestracje?token='.$event->user->verification_token;
        $template_data = ['emailBody'=>'Activation', 'emailTitle'=>'Activation', 'link' => $link];
        Mail::send('mail.user.register', $template_data, function($message) use($request){
                $message->to($event->user->email)->subject('Email Activation');
        });
    }

    /**
     * @param AgentCreated $event
     */
    public function onAgentCreated(AgentCreated $event): void
    {
        // $event->user->verification_token = Str::uuid()->toString();
        // $event->user->save();

        // dispatch(new SendEmailJob(new SetPassword($event->user, SetPassword::AGENT)));

        $link = 'https://dazu.pl/ustaw-haslo?token='.$event->user->verification_token;
        $template_data = ['emailBody'=>'Activation', 'emailTitle'=>'Activation', 'link' => $link];
        Mail::send('mail.user.set_password', $template_data, function($message) use($request){
                $message->to($request->email)->subject('Email Activation');
        });
    }

    public function onNewsletterActivated(NewsletterActivated $event)
    {
        // dispatch(new SendEmailJob(new NewsletterActivatedMail($event->user)));

        $template_data = ['emailBody'=>'', 'emailTitle'=>'', 'link' => ''];
        Mail::send('mail.user.newsletter_activated', $template_data, function($message) use($request){
            $message->to($request->email)->subject('Email Activation');
        });
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

<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\NotificationType;
use App\Events\Offer\OfferActivated;
use App\Events\Offer\PaidOfferCreated;
use App\Events\Offer\OfferCreated;
use App\Mail\Offer\OfferCreated as OfferCreatedMail;
use App\Mail\Offer\PaidOfferCreated as PaidOfferCreatedEmail;
use App\Events\Offer\OfferUpdated;
use App\Jobs\SendEmailJob;
use App\Managers\NotificationManager;
use Illuminate\Contracts\Events\Dispatcher;

class OfferEventSubscriber
{
    /**
     * @var NotificationManager
     */
    protected $notificationManager;

    public function __construct(NotificationManager $notificationManager)
    {
        $this->notificationManager = $notificationManager;
    }

    /**
     * @param OfferCreated $event
     */
    public function onOfferCreated(OfferCreated $event): void
    {
        // dispatch(new SendEmailJob(new OfferCreatedMail($event->offer, $event->user)));
    }

    /**
     * @param OfferUpdated $event
     */
    public function onOfferUpdated(OfferUpdated $event): void
    {
        // TODO: Send notification to all users with subscription and admin
        // dispatch(new SendEmailJob(new OfferUpdatedEmail($event->offer)));
    }

    /**
     * @param PaidOfferCreated $event
     */
    public function onPaidOfferCreated(PaidOfferCreated $event): void
    {
        dispatch(new SendEmailJob(new PaidOfferCreatedEmail($event->offer)));
    }

    public function onOfferActivated(OfferActivated $event)
    {
        dispatch(new SendEmailJob(new OfferCreatedMail($event->offer, $event->offer->user)));
        $link = config('dazu.frontend_url') . '/ogloszenia/' . $event->offer->slug;
        $offerTitle = $event->offer->title;
        $this->notificationManager->store(
            "Ogłoszenie $offerTitle - zostało aktywowane",
            $link,
            NotificationType::INFO,
            $event->offer->user->id
        );
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            OfferCreated::class,
            self::class . '@' . 'onOfferCreated'
        );

        $events->listen(
            OfferUpdated::class,
            self::class . '@' . 'onOfferUpdated'
        );

        $events->listen(
            PaidOfferCreated::class,
            self::class . '@' . 'onPaidOfferCreated'
        );

        $events->listen(
            OfferActivated::class,
            self::class . '@' . 'onOfferActivated'
        );
    }
}

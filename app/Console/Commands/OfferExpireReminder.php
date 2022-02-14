<?php

namespace App\Console\Commands;

use App\Enums\NotificationType;
use App\Managers\NotificationManager;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OfferExpireReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offer:expire-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send offer expired notifications';


    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $notificationManager = new NotificationManager();
        $link = config('dazu.frontend_url') . '/ogloszenia/';
        Offer::where('expire_time', '<=', Carbon::now()->addHours(24))
            ->where('expire_time', '>', Carbon::now())
            ->chunk(100, function ($offers) use ($link, $notificationManager) {
                foreach ($offers as $offer) {
                    $notificationManager->store(
                        "Ogłoszenie $offer->title - wygaśnie w ciągu najbliższych 24h",
                        $link . $offer->slug,
                        NotificationType::WARNING,
                        $offer->user_id
                    );
                }
            });
        $this->info('Notifications send!');
    }
}

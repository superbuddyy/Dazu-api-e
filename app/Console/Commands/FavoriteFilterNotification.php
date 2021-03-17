<?php

namespace App\Console\Commands;

use App\Enums\NotificationType;
use App\Managers\FavoriteFilterManager;
use App\Managers\NotificationManager;
use App\Models\FavoriteFilter;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FavoriteFilterNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filters:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications with offers to users';


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
        $filtersManager = new FavoriteFilterManager();
        FavoriteFilter::where('notification', true)
            ->where('next_notification_date', '<', Carbon::now())
            ->chunk(50, function ($filters) use ($filtersManager) {
                foreach ($filters as $filter) {
                    $filtersManager->sendNotification($filter);
                }
            });
        $this->info('Notifications send!');
    }
}

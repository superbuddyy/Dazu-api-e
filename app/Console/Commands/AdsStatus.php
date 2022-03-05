<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enums\OfferStatus;
use App\Models\Offer;
use Carbon\Carbon;

class AdsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ads:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired status in offers table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Offer::where('expire_time', '<=', Carbon::now())
            ->where('status', OfferStatus::ACTIVE)
            ->chunk(100, function ($offers) {
                foreach ($offers as $offer) {
                    $this->info($offer->id.' = '. $offer->slug);
                    $offer->update([
                        'status' => OfferStatus::EXPIRED
                    ]);
                }
            });
        $this->info('Offer status updated!');
    }
}

<?php

declare(strict_types=1);

use App\Enums\OfferStatus;
use App\Models\Offer;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Seeder;

class PhotosTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $ads = Offer::all();
        $this->command->getOutput()->progressStart($ads->count() * 6);

        foreach ($ads as $ad) {
            for ($i = 0; $i <= 5; $i++) {
                factory(Photo::class)->create(['offer_id' => $ad->id]);
                $this->command->getOutput()->progressAdvance();
            }
        }
        $this->command->getOutput()->progressFinish();
    }
}

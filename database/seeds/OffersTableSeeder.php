<?php

declare(strict_types=1);

use App\Enums\OfferStatus;
use App\Models\Attribute;
use App\Models\Offer;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OffersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(User::class);
        $this->command->getOutput()->progressStart(20);
        $subscription = Subscription::find(Subscription::SILVER);
        for ($i = 0; $i <= 20; $i++) {
            $offer = factory(Offer::class)->create(['user_id' => $user, 'status' => OfferStatus::ACTIVE]);
            $offer->subscriptions()
                ->attach(
                    $subscription->id,
                    ['end_date' => Carbon::now()->addHours(60*7)]
                );
            $attribute = Attribute::find(1);
            $offer->attributes()->attach($attribute->id, ['value' => 'sprzedaz']);
            $this->command->getOutput()->progressAdvance();
        }
        $this->command->getOutput()->progressFinish();
    }
}

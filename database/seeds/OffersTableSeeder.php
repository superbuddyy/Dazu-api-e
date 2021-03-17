<?php

declare(strict_types=1);

use App\Enums\OfferStatus;
use App\Models\Attribute;
use App\Models\Offer;
use App\Models\User;
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
        for ($i = 0; $i <= 20; $i++) {
            $offer = factory(Offer::class)->create(['user_id' => $user, 'status' => OfferStatus::ACTIVE]);
            $attribute = Attribute::find(1);
            $offer->attributes()->attach($attribute->id, ['value' => 'sprzedaz']);
            $this->command->getOutput()->progressAdvance();
        }
        $this->command->getOutput()->progressFinish();
    }
}

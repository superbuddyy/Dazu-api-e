<?php

declare(strict_types=1);

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->getOutput()->progressStart(4);
        Subscription::create([
            'name' => 'Darmowe',
            'price' => '0',
            'duration' => '0',
            'number_of_refreshes' => '1',
            'refresh_price' => '299',
            'number_of_raises' => '0',
            'raise_price' => '1',
            'bargain_price' => '499',
            'urgent_price' => '499',
            'raise_price_three' => '399',
            'raise_price_ten' => '999'
        ]);
        $this->command->getOutput()->progressAdvance();
        Subscription::create([
            'name' => 'Standardowe',
            'price' => '399',
            'duration' => '168',
            'number_of_refreshes' => '2',
            'refresh_price' => '299',
            'number_of_raises' => '0',
            'raise_price' => '199',
            'bargain_price' => '499',
            'urgent_price' => '499',
            'raise_price_three' => '399',
            'raise_price_ten' => '999'
        ]);
        $this->command->getOutput()->progressAdvance();

        Subscription::create([
            'name' => 'Srebrne',
            'price' => '999',
            'duration' => '168',
            'number_of_refreshes' => '3',
            'refresh_price' => '299',
            'number_of_raises' => '0',
            'raise_price' => '199',
            'bargain_price' => '499',
            'urgent_price' => '499',
            'raise_price_three' => '399',
            'raise_price_ten' => '999'
        ]);
        $this->command->getOutput()->progressAdvance();

        Subscription::create([
            'name' => 'Złote',
            'price' => '4999',
            'duration' => '168',
            'number_of_refreshes' => '6',
            'refresh_price' => '299',
            'number_of_raises' => '7',
            'raise_price' => '199',
            'bargain_price' => '499',
            'urgent_price' => '499',
            'raise_price_three' => '399',
            'raise_price_ten' => '999'
        ]);

        $this->command->getOutput()->progressFinish();
    }
}

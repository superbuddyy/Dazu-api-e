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
            'name' => 'Free',
            'description' => '3 darmowe odświeżenia;3.99 za podbicie;Promowanie',
            'price' => '0',
            'duration' => '0',
            'number_of_refreshes' => '2',
            'refresh_price' => '1',
            'number_of_raises' => '0',
            'raise_price' => '1',
        ]);
        $this->command->getOutput()->progressAdvance();
        Subscription::create([
            'name' => 'Standard',
            'description' => '3 darmowe odświeżenia;3.99 za podbicie;Promowanie',
            'price' => '100',
            'duration' => '168',
            'number_of_refreshes' => '2',
            'refresh_price' => '1',
            'number_of_raises' => '0',
            'raise_price' => '1',
        ]);
        $this->command->getOutput()->progressAdvance();

        Subscription::create([
            'name' => 'Silver',
            'description' => '3 darmowe odświeżenia;3.99 za podbicie;Promowanie',
            'price' => '500',
            'duration' => '168',
            'number_of_refreshes' => '2',
            'refresh_price' => '1',
            'number_of_raises' => '0',
            'raise_price' => '1',
        ]);
        $this->command->getOutput()->progressAdvance();

        Subscription::create([
            'name' => 'Gold',
            'description' => '3 darmowe odświeżenia;3.99 za podbicie;Promowanie',
            'price' => '1000',
            'duration' => '168',
            'number_of_refreshes' => '2',
            'refresh_price' => '1',
            'number_of_raises' => '7',
            'raise_price' => '1',
        ]);

        $this->command->getOutput()->progressFinish();
    }
}

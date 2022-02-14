<?php

declare(strict_types=1);

use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->getOutput()->progressStart(4);
        factory(Transaction::class)->create([
            'name' => 'Jan Kowalski',
            'code' => '123456789',
            'address' => 'Warszawa, ul. Szybka 12',
        ]);
        $this->command->getOutput()->progressAdvance();
        factory(Transaction::class)->create([
            'name' => 'Maria Kowalska',
            'code' => '123456789',
            'address' => 'Warszawa, ul. Szybka 12',
        ]);
        $this->command->getOutput()->progressAdvance();
        factory(Transaction::class)->create([
            'name' => 'Marek Kowalski',
            'code' => '',
            'address' => 'Warszawa, ul. Szybka 12',
        ]);
        $this->command->getOutput()->progressAdvance();
        factory(Transaction::class)->create([
            'name' => 'Karol Kowalski',
            'code' => '',
            'address' => 'Warszawa, ul. Szybka 12',
        ]);
        $this->command->getOutput()->progressAdvance();
        factory(Transaction::class)->create([
            'name' => 'User Dazu',
            'code' => '',
            'address' => 'Warszawa, ul. Dazu 12',
            'user_id' => User::where('email', 'user@dazu.app')->first()->id
        ]);

        $this->command->getOutput()->progressFinish();
    }
}

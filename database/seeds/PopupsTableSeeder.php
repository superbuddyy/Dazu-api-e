<?php

declare(strict_types=1);

use App\Models\Popup;
use Illuminate\Database\Seeder;

class PopupsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->getOutput()->progressStart(1);
        factory(Popup::class)->create();
        $this->command->getOutput()->progressAdvance();
        $this->command->getOutput()->progressFinish();
    }
}

<?php

declare(strict_types=1);

use App\Models\Footer;
use Illuminate\Database\Seeder;

class FootersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->getOutput()->progressStart(4);
        factory(Footer::class)->create();
        $this->command->getOutput()->progressAdvance();
        factory(Footer::class)->create();
        $this->command->getOutput()->progressAdvance();
        factory(Footer::class)->create();
        $this->command->getOutput()->progressAdvance();
        factory(Footer::class)->create();
        $this->command->getOutput()->progressFinish();
    }
}

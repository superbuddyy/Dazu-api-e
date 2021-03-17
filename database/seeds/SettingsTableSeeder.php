<?php

declare(strict_types=1);

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Order is important if you wan to add new setting and it at the end
        $this->command->getOutput()->progressStart(4);
        Setting::create([
            'name' => 'link.price',
            'category' => 'pricing',
            'value' => '100',
        ]);
        $this->command->getOutput()->progressAdvance();

        Setting::create([
            'name' => 'photo.price',
            'category' => 'pricing',
            'value' => '100',
        ]);
        $this->command->getOutput()->progressAdvance();

        Setting::create([
            'name' => 'urgent.price',
            'category' => 'pricing',
            'value' => '500',
        ]);

        $this->command->getOutput()->progressAdvance();

        Setting::create([
            'name' => 'company_avatar.price',
            'category' => 'pricing',
            'value' => '500',
        ]);
        $this->command->getOutput()->progressAdvance();

        Setting::create([
            'name' => 'visible_from_date.price',
            'category' => 'pricing',
            'value' => '500',
        ]);
        $this->command->getOutput()->progressAdvance();

        Setting::create([
            'name' => 'avatar.price',
            'category' => 'pricing',
            'value' => '500',
        ]);

        Setting::create([
            'name' => 'video_avatar.price',
            'category' => 'pricing',
            'value' => '500',
        ]);
        $this->command->getOutput()->progressAdvance();

        $this->command->getOutput()->progressFinish();
    }
}

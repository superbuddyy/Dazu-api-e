<?php

use App\Models\Company;
use App\Models\FaqItem;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use App\Laravue\Acl;
use App\Laravue\Models\Role;
use Illuminate\Support\Facades\Hash;

class FaqItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->getOutput()->progressStart(4);
        factory(FaqItem::class)->create();
        $this->command->getOutput()->progressAdvance();
        factory(FaqItem::class)->create();
        $this->command->getOutput()->progressAdvance();
        factory(FaqItem::class)->create();
        $this->command->getOutput()->progressAdvance();
        factory(FaqItem::class)->create();
        $this->command->getOutput()->progressFinish();
    }
}

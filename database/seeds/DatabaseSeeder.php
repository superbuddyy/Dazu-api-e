<?php

declare(strict_types=1);

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(CategoriesTableSeeder::class);
        $this->call(AttributesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
//        $this->call(OffersTableSeeder::class);
//        $this->call(PhotosTableSeeder::class);
        $this->call(SubscriptionsTableSeeder::class);
        $this->call(PostsTableSeeder::class);
//        $this->call(TransactionsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(FaqItemsTableSeeder::class);
        $this->call(PopupsTableSeeder::class);
    }
}

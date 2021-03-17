<?php

declare(strict_types=1);

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->getOutput()->progressStart(4);
        factory(Post::class)->create();
        $this->command->getOutput()->progressAdvance();
        factory(Post::class)->create();
        $this->command->getOutput()->progressAdvance();
        factory(Post::class)->create();
        $this->command->getOutput()->progressAdvance();
        factory(Post::class)->create();
        $this->command->getOutput()->progressFinish();
    }
}

<?php

namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class deleteUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force delete last 6 month';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $query = User::onlyTrashed()
        ->where('deleted_at', '>=', Carbon::now()->subMonths(6))
        ->toSql();
        $this->info(Carbon::now()->subMonths(6));
        $this->info($query);
        User::onlyTrashed()
            ->where('deleted_at', '<=', Carbon::now()->subMonths(6))
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    $this->info($user->id.' = '. $user->email);
                    $user->offers()->delete();
                    $user->forceDelete();
                }
            });
        $this->info('User deleted!');
    }
}

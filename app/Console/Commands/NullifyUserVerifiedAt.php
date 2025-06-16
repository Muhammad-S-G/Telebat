<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Traits\DateFormatTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NullifyUserVerifiedAt extends Command
{
    protected $signature = 'users:nullify-verified-at {--log}';
    protected $description = 'Set verified_at to null for all users every month';

    public function handle()
    {
        $oneMonthAgo = Carbon::now()->subMonth();

        $affected = User::whereNotNull('verified_at')
            ->where('verified_at', '<', $oneMonthAgo)
            ->update(['verified_at' => null]);

        $this->info("{$affected} users had their verified_at nulled."); 
        if ($this->option('log')) {
            \Log::info("{$affected} users had their verified_at nulled.");
        }
    }
}

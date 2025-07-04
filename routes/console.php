<?php

use App\Console\Commands\NullifyUserVerifiedAt;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command(NullifyUserVerifiedAt::class, ['--log'])->monthly(); 

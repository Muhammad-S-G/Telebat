<?php

namespace App\Scheduled;

use App\Models\User;

class NullifyUserConfirmedAt
{

    public function __invoke()
    {
        User::whereNotNull('confirmed_at')->update(['confirmed_at' => null]);
    }
}

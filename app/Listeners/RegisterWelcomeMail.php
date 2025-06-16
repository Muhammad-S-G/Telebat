<?php

namespace App\Listeners;

use App\Mail\RegisterMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class RegisterWelcomeMail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event)
    {
        if (!$event->update_email) {
            $user = $event->user;
            Mail::to($user->email)->send(new RegisterMail($user));
        }
    }
}

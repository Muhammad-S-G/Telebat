<?php

namespace App\Providers;

use App\Listeners\RegisterWelcomeMail;
use App\Models\Notification;
use App\Models\Order;
use App\Policies\NotificationPolicy;
use Illuminate\Notifications\DatabaseNotification;
use App\Policies\OrderPolicy;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientInterface::class, Client::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            Registered::class,
            RegisterWelcomeMail::class
        );

        Gate::policy(Order::class, OrderPolicy::class);
    }
}

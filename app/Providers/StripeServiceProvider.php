<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;
use Stripe\StripeClient;

class StripeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function () {
            Stripe::setApiKey(config('services.stripe.secret'));
            return new StripeClient(config('services.stripe.secret'));
        });
    }

    public function boot(): void {}
}

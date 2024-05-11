<?php

namespace App\Providers;

//use Laravel\Passport\Passport;
//use App\Models\Passport\PersonalAccessClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Passport::usePersonalAccessClientModel(PersonalAccessClient::class);
    }
}

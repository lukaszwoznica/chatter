<?php

namespace App\Providers;

use App\Http\Controllers\Api\V1\FilepondController;
use Illuminate\Support\ServiceProvider;
use Sopamo\LaravelFilepond\Http\Controllers\FilepondController as BaseFilepondController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->bind(BaseFilepondController::class, FilepondController::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

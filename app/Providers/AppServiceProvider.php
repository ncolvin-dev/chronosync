<?php

namespace App\Providers;

use App\Contracts\SnsTopicServiceInterface;
use App\Services\FakeSnsTopicService;
use App\Services\SnsTopicService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SnsTopicServiceInterface::class, function () {
            if ($this->app->environment('testing') || config('chronosync.sns.fake')) {
                return new FakeSnsTopicService();
            }
            return new SnsTopicService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.default');
    }
}

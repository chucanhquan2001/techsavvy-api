<?php

namespace App\Infrastructure\Providers;

use App\Domain\Repositories\UserVisitRepositoryInterface;
use App\Infrastructure\Persistence\EloquentUserVisitRepository;
use Illuminate\Support\ServiceProvider;

class UserVisitServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserVisitRepositoryInterface::class,
            EloquentUserVisitRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

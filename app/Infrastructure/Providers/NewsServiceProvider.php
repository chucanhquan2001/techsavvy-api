<?php

namespace App\Infrastructure\Providers;

use App\Domain\Repositories\NewsRepositoryInterface;
use App\Infrastructure\Persistence\EloquentNewsRepository;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(NewsRepositoryInterface::class, EloquentNewsRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

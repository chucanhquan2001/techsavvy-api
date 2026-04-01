<?php

namespace App\Infrastructure\Providers;

use App\Domain\Repositories\ContactRepositoryInterface;
use App\Infrastructure\Persistence\EloquentContactRepository;
use Illuminate\Support\ServiceProvider;

class ContactServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ContactRepositoryInterface::class, EloquentContactRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

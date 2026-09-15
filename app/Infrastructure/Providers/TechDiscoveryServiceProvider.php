<?php

namespace App\Infrastructure\Providers;

use App\Application\TechDiscovery\UseCases\SyncTechTrendsUseCase;
use App\Infrastructure\TechDiscovery\Providers\GitHubTrendingProvider;
use App\Infrastructure\TechDiscovery\Providers\TechNewsFeedProvider;
use App\Infrastructure\TechDiscovery\TechTrendPersistenceService;
use Illuminate\Support\ServiceProvider;

class TechDiscoveryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SyncTechTrendsUseCase::class, function ($app): SyncTechTrendsUseCase {
            return new SyncTechTrendsUseCase(
                providers: [
                    $app->make(GitHubTrendingProvider::class),
                    $app->make(TechNewsFeedProvider::class),
                ],
                persistenceService: $app->make(TechTrendPersistenceService::class),
            );
        });
    }
}

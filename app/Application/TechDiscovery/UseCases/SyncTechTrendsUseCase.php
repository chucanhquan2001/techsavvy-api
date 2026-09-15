<?php

namespace App\Application\TechDiscovery\UseCases;

use App\Application\TechDiscovery\Contracts\TechTrendProviderInterface;
use App\Application\TechDiscovery\DTOs\TechTrendRecord;
use App\Infrastructure\TechDiscovery\TechTrendPersistenceService;
use Illuminate\Support\Facades\Log;

class SyncTechTrendsUseCase
{
    /**
     * @param  array<TechTrendProviderInterface>  $providers
     */
    public function __construct(
        private readonly array $providers,
        private readonly TechTrendPersistenceService $persistenceService,
    ) {}

    public function execute(): array
    {
        $records = [];
        $failures = [];

        foreach ($this->providers as $provider) {
            try {
                $fetched = $provider->fetch();

                foreach ($fetched as $record) {
                    if ($record instanceof TechTrendRecord) {
                        $records[] = $record;
                    }
                }

                if ($fetched === []) {
                    $failures[] = $provider->key();
                }
            } catch (\Throwable $exception) {
                $failures[] = $provider->key();
                Log::warning('Tech trend provider sync failed', [
                    'provider' => $provider->key(),
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $saved = $this->persistenceService->persist($records);

        return [
            'records_count' => count($records),
            'saved_count' => $saved,
            'failed_providers' => array_values(array_unique($failures)),
        ];
    }
}

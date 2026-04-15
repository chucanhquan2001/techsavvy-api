<?php

namespace App\Application\MarketPrice\UseCases;

use App\Application\MarketPrice\Contracts\MarketPriceProviderInterface;
use App\Application\MarketPrice\DTOs\MarketPriceRecord;
use App\Infrastructure\MarketPrice\MarketPricePersistenceService;
use Illuminate\Support\Facades\Log;

class SyncMarketPricesUseCase
{
    /**
     * @param array<MarketPriceProviderInterface> $providers
     */
    public function __construct(
        private readonly array $providers,
        private readonly MarketPricePersistenceService $persistenceService,
    ) {}

    public function execute(): array
    {
        $records = [];
        $failures = [];

        foreach ($this->providers as $provider) {
            try {
                $fetched = $provider->fetch();

                foreach ($fetched as $record) {
                    if ($record instanceof MarketPriceRecord) {
                        $records[] = $record;
                    }
                }

                if ($fetched === []) {
                    $failures[] = $provider->key();
                }
            } catch (\Throwable $exception) {
                $failures[] = $provider->key();
                Log::warning('Market provider sync failed', [
                    'provider' => $provider->key(),
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        $this->persistenceService->persist($records);

        return [
            'records_count' => count($records),
            'failed_providers' => array_values(array_unique($failures)),
        ];
    }
}

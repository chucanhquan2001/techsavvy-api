<?php

namespace App\Infrastructure\MarketPrice;

use App\Application\MarketPrice\DTOs\MarketPriceRecord;
use App\Models\MarketPriceHistory;
use App\Models\MarketPriceSnapshot;
use Illuminate\Support\Facades\DB;

class MarketPricePersistenceService
{
    /**
     * @param array<MarketPriceRecord> $records
     */
    public function persist(array $records): void
    {
        if ($records === []) {
            return;
        }

        DB::transaction(function () use ($records): void {
            foreach ($records as $record) {
                $payload = $record->toArray();

                MarketPriceSnapshot::updateOrCreate(
                    [
                        'instrument' => $payload['instrument'],
                        'source' => $payload['source'],
                    ],
                    $payload
                );

                MarketPriceHistory::create($payload);
            }
        });
    }
}

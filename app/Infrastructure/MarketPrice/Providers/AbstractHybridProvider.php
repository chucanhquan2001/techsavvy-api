<?php

namespace App\Infrastructure\MarketPrice\Providers;

use Illuminate\Support\Facades\Http;

abstract class AbstractHybridProvider
{
    protected function loadJson(?string $url): ?array
    {
        if (blank($url)) {
            return null;
        }

        try {
            $response = Http::timeout((int) config('services.market_data.timeout_seconds', 10))
                ->retry((int) config('services.market_data.retry_times', 1), 200)
                ->acceptJson()
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            return $response->json();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function loadHtml(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        try {
            $response = Http::timeout((int) config('services.market_data.timeout_seconds', 10))
                ->retry((int) config('services.market_data.retry_times', 1), 200)
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            return $response->body();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function numberFromHtml(?string $html, string $pattern = '/([0-9]+(?:[.,][0-9]+)?)/'): ?float
    {
        if (blank($html)) {
            return null;
        }

        if (! preg_match($pattern, $html, $matches)) {
            return null;
        }

        return $this->toFloat($matches[1]);
    }

    protected function toFloat(string|int|float|null $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $normalized = preg_replace('/\s+/', '', trim($value));
        if ($normalized === null || $normalized === '') {
            return null;
        }

        if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
            $normalized = str_replace(',', '', $normalized);
        } elseif (str_contains($normalized, ',')) {
            $normalized = str_replace(',', '.', $normalized);
        }

        return is_numeric($normalized) ? (float) $normalized : null;
    }
}

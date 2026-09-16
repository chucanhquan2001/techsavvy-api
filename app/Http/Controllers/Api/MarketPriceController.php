<?php

namespace App\Http\Controllers\Api;

use App\Enums\HttpStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListMarketPriceHistoryRequest;
use App\Http\Requests\ListMarketPricesRequest;
use App\Models\MarketPriceHistory;
use App\Models\MarketPriceSnapshot;
use Illuminate\Http\Request;
use Throwable;

/**
 * @group Market Prices
 */
class MarketPriceController extends Controller
{
    public function index(ListMarketPricesRequest $request)
    {
        try {
            $items = MarketPriceSnapshot::query()
                ->filter($request->validated())
                ->latest('quoted_at')
                ->latest('id')
                ->paginate($request->perPage());

            return ApiResponse::paginated($items, 'Market prices fetched successfully');
        } catch (Throwable $e) {
            return ApiResponse::error('Failed to fetch market prices', $e);
        }
    }

    public function show(Request $request, string $instrument)
    {
        try {
            $item = MarketPriceSnapshot::query()
                ->where('instrument', $instrument)
                ->when($request->filled('source'), fn ($query) => $query->where('source', (string) $request->string('source')))
                ->latest('quoted_at')
                ->latest('id')
                ->first();

            if (! $item) {
                return ApiResponse::fail('Market price not found', [], HttpStatus::NOT_FOUND);
            }

            return ApiResponse::ok($item, 'Market price fetched successfully');
        } catch (Throwable $e) {
            return ApiResponse::error('Failed to fetch market price', $e);
        }
    }

    public function history(ListMarketPriceHistoryRequest $request, string $instrument)
    {
        try {
            $items = MarketPriceHistory::query()
                ->where('instrument', $instrument)
                ->filter($request->validated())
                ->latest('quoted_at')
                ->latest('id')
                ->paginate($request->perPage());

            return ApiResponse::paginated($items, 'Market price history fetched successfully');
        } catch (Throwable $e) {
            return ApiResponse::error('Failed to fetch market price history', $e);
        }
    }
}

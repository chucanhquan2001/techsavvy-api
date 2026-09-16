<?php

namespace App\Http\Controllers\Api;

use App\Enums\HttpStatus;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListTechTrendsRequest;
use App\Models\TechTrendItem;
use Throwable;

/**
 * @group Tech Trends
 */
class TechTrendController extends Controller
{
    public function index(ListTechTrendsRequest $request)
    {
        try {
            $items = TechTrendItem::query()
                ->filter($request->validated())
                ->latest('trend_date')
                ->latest('published_at')
                ->latest('id')
                ->paginate($request->perPage());

            return ApiResponse::paginated($items, 'Tech trends fetched successfully');
        } catch (Throwable $e) {
            return ApiResponse::error('Failed to fetch tech trends', $e);
        }
    }

    public function show(string $slug)
    {
        try {
            $item = TechTrendItem::query()
                ->where('slug', $slug)
                ->when(is_numeric($slug), fn ($query) => $query->orWhere('id', (int) $slug))
                ->first();

            if (! $item) {
                return ApiResponse::fail('Tech trend not found', [], HttpStatus::NOT_FOUND);
            }

            return ApiResponse::ok($item, 'Tech trend fetched successfully');
        } catch (Throwable $e) {
            return ApiResponse::error('Failed to fetch tech trend', $e);
        }
    }
}

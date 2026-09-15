<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\TechTrendItem;
use Illuminate\Http\Request;
use Throwable;

class TechTrendController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = min(max((int) $request->integer('per_page', 20), 1), 100);

            $items = TechTrendItem::query()
                ->when($request->filled('type'), fn ($query) => $query->where('type', (string) $request->string('type')))
                ->when($request->filled('source'), fn ($query) => $query->where('source', (string) $request->string('source')))
                ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->string('status')))
                ->when($request->filled('date'), fn ($query) => $query->whereDate('trend_date', $request->date('date')))
                ->when($request->filled('technology'), function ($query) use ($request) {
                    $technology = (string) $request->string('technology');
                    $query->where(function ($nested) use ($technology) {
                        $nested->whereJsonContains('technologies', $technology)
                            ->orWhereJsonContains('topics', $technology)
                            ->orWhere('language', $technology);
                    });
                })
                ->latest('trend_date')
                ->latest('published_at')
                ->latest('id')
                ->paginate($perPage);

            return ApiResponse::ok($items, 'Tech trends fetched successfully');
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
                return ApiResponse::fail('Tech trend not found', [], 404);
            }

            return ApiResponse::ok($item, 'Tech trend fetched successfully');
        } catch (Throwable $e) {
            return ApiResponse::error('Failed to fetch tech trend', $e);
        }
    }
}

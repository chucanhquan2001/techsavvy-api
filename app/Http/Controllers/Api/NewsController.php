<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Application\News\UseCases\{
    CreateNewsUseCase,
    GetNewsListUseCase,
    UpdateNewsUseCase,
    DeleteNewsUseCase
};
use App\Application\News\Commands\{
    CreateNewsCommand,
    UpdateNewsCommand
};
use App\Enums\HttpStatus;
use App\Helpers\ApiResponse;
use Throwable;

class NewsController extends Controller
{
    public function index(GetNewsListUseCase $useCase)
    {
        try {
            $data = $useCase->execute();
            return ApiResponse::ok($data, 'List fetched successfully');
        } catch (Throwable $e) {
            return ApiResponse::error('Failed to fetch news list', $e);
        }
    }

    public function store(Request $req, CreateNewsUseCase $useCase)
    {
        try {
            $cmd = new CreateNewsCommand($req->title, $req->content);
            $news = $useCase->execute($cmd);
            return ApiResponse::ok($news, 'News created successfully', HttpStatus::CREATED);
        } catch (Throwable $e) {
            return ApiResponse::fail('Failed to create news', [$e->getMessage()]);
        }
    }

    public function update($id, Request $req, UpdateNewsUseCase $useCase)
    {
        try {
            $cmd = new UpdateNewsCommand($id, $req->title, $req->content);
            $news = $useCase->execute($cmd);
            if (!$news) return ApiResponse::fail('News not found', [], HttpStatus::NOT_FOUND);
            return ApiResponse::ok($news, 'News updated successfully');
        } catch (Throwable $e) {
            return ApiResponse::error('Update failed', $e);
        }
    }

    public function destroy($id, DeleteNewsUseCase $useCase)
    {
        try {
            $useCase->execute($id);
            return ApiResponse::ok(null, 'Deleted successfully');
        } catch (Throwable $e) {
            return ApiResponse::error('Failed to delete', $e);
        }
    }
}
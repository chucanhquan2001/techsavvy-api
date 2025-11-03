<?php

namespace App\Helpers;

use App\Enums\HttpStatus;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function ok($data = null, string $message = 'Success', int $code = HttpStatus::OK): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'path' => request()->path(),
            ]
        ], $code);
    }

    public static function fail(string $message = 'Invalid request', array $errors = [], int $code = HttpStatus::BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status' => 'fail',
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'path' => request()->path(),
            ]
        ], $code);
    }

    public static function error(string $message = 'Server error', ?\Throwable $exception = null, int $code = HttpStatus::INTERNAL_SERVER_ERROR): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
            'errors' => $exception ? [$exception->getMessage()] : null,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'path' => request()->path(),
            ]
        ], $code);
    }

    public static function unauthorized(string $message = 'Unauthorized', int $code = HttpStatus::UNAUTHORIZED): JsonResponse
    {
        return response()->json([
            'status' => 'unauthorized',
            'message' => $message,
            'data' => null,
            'errors' => null,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'path' => request()->path(),
            ]
        ], $code);
    }
}
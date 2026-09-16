<?php

namespace App\Http\Requests;

use App\Enums\HttpStatus;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ListTechTrendsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'type' => ['sometimes', 'string', 'max:32'],
            'source' => ['sometimes', 'string', 'max:64'],
            'status' => ['sometimes', 'string', 'max:32'],
            'technology' => ['sometimes', 'string', 'max:128'],
            'date' => ['sometimes', 'date'],
            'from' => ['sometimes', 'date'],
            'to' => ['sometimes', 'date', 'after_or_equal:from'],
            'q' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function perPage(): int
    {
        return min(max((int) $this->integer('per_page', 20), 1), 100);
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ApiResponse::fail(
                'Validation failed',
                $validator->errors()->toArray(),
                HttpStatus::UNPROCESSABLE_ENTITY
            )
        );
    }
}

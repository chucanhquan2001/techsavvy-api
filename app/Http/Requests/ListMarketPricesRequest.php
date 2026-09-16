<?php

namespace App\Http\Requests;

use App\Enums\HttpStatus;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ListMarketPricesRequest extends FormRequest
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
            'category' => ['sometimes', 'string', 'max:64'],
            'instrument' => ['sometimes', 'string', 'max:64'],
            'currency' => ['sometimes', 'string', 'max:10'],
            'source' => ['sometimes', 'string', 'max:128'],
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

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

abstract class BaseFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        $response = response()->json([
            'success' => false,
            'errors' => (new ValidationException($validator))->errors(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}

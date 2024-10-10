<?php

namespace App\Http\Requests\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * If the validation fails, throw a ValidationException
     * with the failed validator and a custom response.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function failedValidation(Validator $validator)
    {
        // Throw a ValidationException with the failed validator and a custom response
        // The response will be a JSON response with a 401 status code
        // containing the validation errors
        throw new ValidationException($validator, response()->json([
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ], 401));
    }
}

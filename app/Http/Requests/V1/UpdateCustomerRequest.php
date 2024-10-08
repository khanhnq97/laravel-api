<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
        $method = $this->method();

        if ($method === 'PUT') {
            return [
                'name' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', 'max:255', Rule::in(['Company', 'Individual'])],
                'email' => ['required', 'string', 'email', 'max:255',],
                'address' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:255'],
                'state' => ['required', 'string', 'max:255'],
                'postalCode' => ['required', 'string', 'max:255'],
            ];
        } else {
            return [
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'type' => ['sometimes', 'required', 'string', 'max:255', Rule::in(['Company', 'Individual'])],
                'email' => ['sometimes', 'required', 'string', 'email', 'max:255',],
                'address' => ['sometimes', 'required', 'string', 'max:255'],
                'city' => ['sometimes', 'required', 'string', 'max:255'],
                'state' => ['sometimes', 'required', 'string', 'max:255'],
                'postalCode' => ['sometimes', 'required', 'string', 'max:255'],
            ];
        }
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('postalCode')) {
            $this->merge([
                'postal_code' => $this->postalCode,
            ]);
        }
    }
}

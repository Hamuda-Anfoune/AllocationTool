<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUniversityUserRequest extends FormRequest
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
            'account_type_id' => ['required', 'string', 'max:255', 'exists:account_types,account_type_id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:university_users'],
        ];
    }
}

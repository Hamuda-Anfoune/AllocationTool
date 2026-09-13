<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
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
            'module_name' => ['required', 'string', 'max:255', 'unique:modules'],
            'module_id' => ['required', 'string', 'max:255', 'unique:modules'],
            'academic_year' => ['required', 'string', 'max:255', 'exists:academic_years,year'],
            'convenor_email' => ['required', 'string', 'min:8', 'exists:university_users,email'],
        ];
    }
}

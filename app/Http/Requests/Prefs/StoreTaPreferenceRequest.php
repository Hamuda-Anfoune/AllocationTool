<?php

namespace App\Http\Requests\Prefs;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaPreferenceRequest extends FormRequest
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
            'module_1_id' => ['required', 'string', 'exists:modules,module_id'],
            'max_modules' => ['required', 'integer'],
            'max_contact_hours' => ['required', 'integer'],
            'max_marking_hours' => ['required', 'integer', 'numeric', 'min:7'],
            'academic_year' => ['required', 'string', 'exists:academic_years,year'],
            'have_tier4_visa' => ['nullable', 'boolean'],
            'done_before_1' => ['nullable', 'boolean'],
            'done_before_2' => ['nullable', 'boolean'],
            'done_before_3' => ['nullable', 'boolean'],
            'done_before_4' => ['nullable', 'boolean'],
            'done_before_5' => ['nullable', 'boolean'],
            'done_before_6' => ['nullable', 'boolean'],
            'done_before_7' => ['nullable', 'boolean'],
            'done_before_8' => ['nullable', 'boolean'],
            'done_before_9' => ['nullable', 'boolean'],
            'done_before_10' => ['nullable', 'boolean'],
            'module_2_id' => ['nullable', 'different:module_1_id'],
            'module_3_id' => ['nullable', 'different:module_1_id', 'different:module_2_id'],
            'module_4_id' => ['nullable', 'different:module_1_id', 'different:module_2_id', 'different:module_3_id'],
            'module_5_id' => ['nullable', 'different:module_1_id', 'different:module_2_id', 'different:module_3_id', 'different:module_4_id'],
            'module_6_id' => ['nullable', 'different:module_1_id', 'different:module_2_id', 'different:module_3_id', 'different:module_4_id', 'different:module_5_id'],
            'module_7_id' => ['nullable', 'different:module_1_id', 'different:module_2_id', 'different:module_3_id', 'different:module_4_id', 'different:module_5_id', 'different:module_6_id'],
            'module_8_id' => ['nullable', 'different:module_1_id', 'different:module_2_id', 'different:module_3_id', 'different:module_4_id', 'different:module_5_id', 'different:module_6_id', 'different:module_7_id'],
            'module_9_id' => ['nullable', 'different:module_1_id', 'different:module_2_id', 'different:module_3_id', 'different:module_4_id', 'different:module_5_id', 'different:module_6_id', 'different:module_7_id', 'different:module_8_id'],
            'module_10_id' => ['nullable', 'different:module_1_id', 'different:module_2_id', 'different:module_3_id', 'different:module_4_id', 'different:module_5_id', 'different:module_6_id', 'different:module_7_id', 'different:module_8_id', 'different:module_9_id'],
            'preferred_language_1_id' => ['nullable', 'string', 'exists:languages,language_id'],
            'preferred_language_2_id' => ['nullable', 'different:preferred_language_1_id'],
            'preferred_language_3_id' => ['nullable', 'different:preferred_language_1_id', 'different:preferred_language_2_id'],
            'preferred_language_4_id' => ['nullable', 'different:preferred_language_1_id', 'different:preferred_language_2_id', 'different:preferred_language_3_id'],
            'preferred_language_5_id' => ['nullable', 'different:preferred_language_1_id', 'different:preferred_language_2_id', 'different:preferred_language_3_id', 'different:preferred_language_4_id'],
            'preferred_language_6_id' => ['nullable', 'different:preferred_language_1_id', 'different:preferred_language_2_id', 'different:preferred_language_3_id', 'different:preferred_language_4_id', 'different:preferred_language_5_id'],
            'preferred_language_7_id' => ['nullable', 'different:preferred_language_1_id', 'different:preferred_language_2_id', 'different:preferred_language_3_id', 'different:preferred_language_4_id', 'different:preferred_language_5_id', 'different:preferred_language_6_id'],
        ];
    }
}

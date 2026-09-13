<?php

namespace App\Http\Requests\Prefs;

use Illuminate\Foundation\Http\FormRequest;

class StoreModulePreferenceRequest extends FormRequest
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
            'module_id' => ['required', 'string', 'exists:modules,module_id'],
            'no_of_assistants' => ['required', 'integer'],
            'no_of_contact_hours' => ['required', 'integer'],
            'no_of_marking_hours' => ['required', 'integer'],
            'academic_year' => ['required', 'string', 'exists:academic_years,year'],
            'language_1_id' => ['nullable', 'string', 'exists:languages,language_id'],
            'language_2_id' => ['nullable', 'different:language_1_id'],
            'language_3_id' => ['nullable', 'different:language_1_id', 'different:language_2_id'],
            'language_4_id' => ['nullable', 'different:language_1_id', 'different:language_2_id', 'different:language_3_id'],
            'language_5_id' => ['nullable', 'different:language_1_id', 'different:language_2_id', 'different:language_3_id', 'different:language_4_id'],
            'language_6_id' => ['nullable', 'different:language_1_id', 'different:language_2_id', 'different:language_3_id', 'different:language_4_id', 'different:language_5_id'],
            'language_7_id' => ['nullable', 'different:language_1_id', 'different:language_2_id', 'different:language_3_id', 'different:language_4_id', 'different:language_5_id', 'different:language_6_id'],
        ];
    }
}

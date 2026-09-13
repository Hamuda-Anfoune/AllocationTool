<?php

namespace App\Http\Requests\Prefs;

class UpdateModulePreferenceRequest extends StoreModulePreferenceRequest
{
    /**
     * module_id and academic_year are identified by the route ({module}/{academicYear}), not re-submitted in the body.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        unset($rules['module_id'], $rules['academic_year']);

        return $rules;
    }
}

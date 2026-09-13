<?php

namespace App\Http\Requests\Prefs;

class UpdateTaPreferenceRequest extends StoreTaPreferenceRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'preference_id' => ['required', 'string', 'exists:ta_preferences,preference_id'],
        ]);
    }
}

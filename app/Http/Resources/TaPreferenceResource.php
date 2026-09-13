<?php

namespace App\Http\Resources;

use App\Models\TaPreference;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TaPreference
 */
class TaPreferenceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'preference_id' => $this->preference_id,
            'ta_email' => $this->ta_email,
            'max_contact_hours' => $this->max_contact_hours,
            'max_marking_hours' => $this->max_marking_hours,
            'max_modules' => $this->max_modules,
            'academic_year' => $this->academic_year,
            'have_tier4_visa' => $this->have_tier4_visa,
        ];
    }
}

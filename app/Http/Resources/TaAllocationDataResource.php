<?php

namespace App\Http\Resources;

use App\Models\TaAllocationData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TaAllocationData
 */
class TaAllocationDataResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'allocation_id' => $this->allocation_id,
            'ta_id' => $this->ta_id,
            'contact_hours' => $this->contact_hours,
            'marking_hours' => $this->marking_hours,
            'academic_year' => $this->academic_year,
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Allocation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Allocation
 */
class AllocationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'allocation_id' => $this->allocation_id,
            'module_id' => $this->module_id,
            'ta_id' => $this->ta_id,
            'academic_year' => $this->academic_year,
            'creator_email' => $this->creator_email,
            'created_at' => $this->created_at,
            'updated_at' => $this->when(isset($this->updated_at), $this->updated_at),
        ];
    }
}

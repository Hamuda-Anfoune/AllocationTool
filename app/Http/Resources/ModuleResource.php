<?php

namespace App\Http\Resources;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Module
 */
class ModuleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'module_id' => $this->module_id,
            'module_name' => $this->module_name,
            'convenor_email' => $this->convenor_email,
            'academic_year' => $this->academic_year,
        ];
    }
}

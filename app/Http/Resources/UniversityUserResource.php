<?php

namespace App\Http\Resources;

use App\Models\UniversityUser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UniversityUser
 */
class UniversityUserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'email' => $this->email,
            'account_type_id' => $this->account_type_id,
            'account_type' => $this->when(isset($this->account_type), $this->account_type),
            'created_at' => $this->created_at,
        ];
    }
}

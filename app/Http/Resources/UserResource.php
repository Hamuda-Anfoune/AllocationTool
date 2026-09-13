<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'account_type_id' => $this->account_type_id,
            'account_type' => $this->when(isset($this->account_type), $this->account_type),
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->when(isset($this->updated_at), $this->updated_at),
        ];
    }
}

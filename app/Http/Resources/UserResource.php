<?php

namespace App\Http\Resources;

use App\Support\PermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url,
            'gender' => $this->gender?->value,
            'gender_label' => $this->gender?->label(),
            'is_active' => (bool) $this->is_active,
            'is_approved' => $this->isApproved(),
            'permissions' => $this->isSuperAdmin()
                ? PermissionCatalog::allPermissions()
                : ($this->role?->permissions ?? []),
            'role' => $this->role ? [
                'id' => $this->role->id,
                'name' => $this->role->name,
                'display_name' => $this->role->display_name,
            ] : null,
        ];
    }
}

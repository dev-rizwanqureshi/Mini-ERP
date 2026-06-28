<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'display_name', 'description', 'permissions', 'is_system'])]
class Role extends Model
{
    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_system' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function allows(string $ability): bool
    {
        if ($this->name === 'super_admin') {
            return true;
        }

        foreach ($this->permissions ?? [] as $permission) {
            if ($permission === '*' || $permission === $ability) {
                return true;
            }

            if (str_ends_with($permission, '.*') && str_starts_with($ability, substr($permission, 0, -1))) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace Modules\User\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;

trait HasRolesAndPermissions
{
    /**
     * Roles assigned to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Determine if the user has any of the given roles.
     */
    public function hasRole(string|array $roles): bool
    {
        $rolesList = is_array($roles) ? $roles : func_get_args();

        return $this->roles->contains(function ($role) use ($rolesList) {
            return in_array($role->name, $rolesList, true);
        });
    }

    /**
     * Assign a role to the user by name or model.
     */
    public function assignRole(string|Role $role): self
    {
        $roleModel = is_string($role)
            ? Role::where('name', strtolower(trim($role)))->firstOrFail()
            : $role;

        $this->roles()->syncWithoutDetaching([$roleModel->id]);

        return $this;
    }

    /**
     * Sync roles for the user.
     */
    public function syncRoles(iterable $roles): self
    {
        $roleIds = [];

        foreach ($roles as $item) {
            if ($item instanceof Role) {
                $roleIds[] = $item->id;
            } elseif (is_numeric($item)) {
                $roleIds[] = (int) $item;
            } elseif (is_string($item) && ! empty($item)) {
                $roleModel = Role::where('name', strtolower(trim($item)))->first();
                if ($roleModel) {
                    $roleIds[] = $roleModel->id;
                }
            }
        }

        $this->roles()->sync(array_unique($roleIds));

        return $this;
    }

    /**
     * Determine if the user is a super administrator.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(['super-admin', 'super_admin']);
    }

    /**
     * Determine if the user has a specific permission via any assigned role.
     */
    public function hasPermission(string $permissionName): bool
    {
        $permissionKey = strtolower(trim($permissionName));

        // Super Admin or Admin role has all permissions by default
        if ($this->hasRole(['super-admin', 'super_admin', 'admin'])) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionKey)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all unique permissions across all assigned roles.
     */
    public function getAllPermissions(): Collection
    {
        if ($this->hasRole(['super-admin', 'super_admin', 'admin'])) {
            return Permission::all();
        }

        return $this->roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id')->values();
    }
}

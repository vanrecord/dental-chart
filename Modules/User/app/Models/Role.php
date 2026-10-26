<?php

namespace Modules\User\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'label',
        'description',
    ];

    /**
     * The permissions that belong to the role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * The users that belong to the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * Check if role has a given permission by full identifier.
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions->contains('name', strtolower(trim($permissionName)));
    }

    /**
     * Give a permission to the role.
     */
    public function givePermissionTo(string|Permission $permission): self
    {
        $perm = is_string($permission)
            ? Permission::findOrCreate($permission)
            : $permission;

        $this->permissions()->syncWithoutDetaching([$perm->id]);

        return $this;
    }

    /**
     * Sync permissions by names, IDs, models, or collections.
     */
    public function syncPermissions(iterable $permissions): self
    {
        $permissionIds = [];

        foreach ($permissions as $item) {
            if ($item instanceof Permission) {
                $permissionIds[] = $item->id;
            } elseif (is_numeric($item)) {
                $permissionIds[] = (int) $item;
            } elseif (is_string($item) && ! empty($item)) {
                $perm = Permission::findOrCreate($item);
                $permissionIds[] = $perm->id;
            }
        }

        $this->permissions()->sync(array_unique($permissionIds));

        return $this;
    }

    /**
     * Find or create role idempotently.
     */
    public static function findOrCreate(string $name, string $label, ?string $description = null): self
    {
        return static::firstOrCreate(
            ['name' => strtolower(trim($name))],
            [
                'label' => $label,
                'description' => $description,
            ]
        );
    }
}

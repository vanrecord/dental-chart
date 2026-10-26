<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\User\Support\PermissionRegistry;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'module',
        'controller',
        'action',
        'label',
        'description',
    ];

    /**
     * The roles associated with this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    /**
     * Get the human-readable action label (e.g. "View", "Create").
     */
    public function getActionLabelAttribute(): string
    {
        return $this->label ?: PermissionRegistry::actionLabel($this->action);
    }

    /**
     * Get the human-readable module label.
     */
    public function getModuleLabelAttribute(): string
    {
        return PermissionRegistry::moduleLabel($this->module);
    }

    /**
     * Get the human-readable controller label.
     */
    public function getControllerLabelAttribute(): string
    {
        return PermissionRegistry::controllerLabel($this->controller);
    }

    /**
     * Find or create a permission idempotently, automatically extracting module, controller, and action.
     */
    public static function findOrCreate(string $name, ?string $label = null, ?string $description = null): self
    {
        $parsed = PermissionRegistry::parse($name);
        $action = $parsed['function'];
        $displayLabel = $label ?: PermissionRegistry::actionLabel($action);

        return static::firstOrCreate(
            ['name' => strtolower(trim($name))],
            [
                'module' => $parsed['module'],
                'controller' => $parsed['controller'],
                'action' => $action,
                'label' => $displayLabel,
                'description' => $description,
            ]
        );
    }
}


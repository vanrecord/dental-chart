<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\User\Traits\HasRolesAndPermissions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_super_addmin',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_addmin' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * User's personal profile relationship.
     */
    public function profile()
    {
        return $this->hasOne(\Modules\Hospital\Models\Profile::class);
    }

    /**
     * User's doctor professional record relationship.
     */
    public function doctor()
    {
        return $this->hasOne(\Modules\Hospital\Models\Doctor::class);
    }

    /**
     * Check if user account is active.
     */
    public function getIsActiveAttribute(): bool
    {
        return (bool) ($this->attributes['is_active'] ?? true);
    }

    /**
     * Alias accessor for is_super_admin.
     */
    public function getIsSuperAdminAttribute(): bool
    {
        return (bool) ($this->attributes['is_super_addmin'] ?? false);
    }

    /**
     * Alias mutator for is_super_admin.
     */
    public function setIsSuperAdminAttribute(mixed $value): void
    {
        $this->attributes['is_super_addmin'] = (bool) $value;
    }
}

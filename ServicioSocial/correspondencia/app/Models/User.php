<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'department_id',
        'name',
        'email',
        'password',
        'position',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function assignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Assignment::class, 'assigned_to_user_id');
    }

    public function assignedDocuments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Document::class, 'current_assigned_user_id');
    }

    public function assignedByMe(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Assignment::class, 'assigned_by');
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return $this->hasAnyRole($roles);
        }

        return $this->roles->where('is_active', true)->contains('name', $roles);
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->where('is_active', true)->whereIn('name', $roles)->isNotEmpty();
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->roles()
            ->where('is_active', true)
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->contains('name', $permissionName);
    }

    public function assignRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role->id);
    }

    public function syncRoles(array $roleIds): void
    {
        $this->roles()->sync($roleIds);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function getPrimaryRoleAttribute(): ?Role
    {
        return $this->roles->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

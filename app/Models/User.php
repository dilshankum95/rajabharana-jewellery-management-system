<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Support\Rbac;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'city',
        'profile_photo_path',
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
            'role' => UserRole::class,
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::Staff;
    }

    public function isStaffMember(): bool
    {
        return $this->role instanceof UserRole && $this->role->isPanelRole();
    }

    public function isAdminOrStaff(): bool
    {
        return $this->isStaffMember();
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::Manager;
    }

    public function isTechnician(): bool
    {
        return $this->role === UserRole::Technician;
    }

    public function isManagedStaffAccount(): bool
    {
        return $this->role instanceof UserRole && $this->role->isManagedStaffAccount();
    }

    public function assignedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'assigned_technician_id');
    }

    public function reportExports(): HasMany
    {
        return $this->hasMany(ReportExport::class, 'generated_by');
    }

    public function hasPermission(string $permission): bool
    {
        return Rbac::userHasPermission($this, $permission);
    }

    /** @param  list<string>  $permissions */
    public function hasAnyPermission(array $permissions): bool
    {
        return Rbac::userHasAnyPermission($this, $permissions);
    }

    public function scopeStaffAccounts($query)
    {
        $roles = array_map(
            fn (UserRole $role) => $role->value,
            UserRole::panelRoles()
        );
        $roles[] = UserRole::Technician->value;

        return $query->whereIn('role', $roles);
    }

    public function scopeTechnicians($query)
    {
        return $query->where('role', UserRole::Technician);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path
            ? asset('storage/'.$this->profile_photo_path)
            : null;
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name), 2);

        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1).substr($parts[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 1));
    }
}

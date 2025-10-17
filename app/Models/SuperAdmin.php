<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SuperAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function boot()
    {
        parent::boot();

        // Prevent multiple master admins
        static::saving(function ($superAdmin) {
            if ($superAdmin->is_master) {
                // Check if another master admin already exists
                $existingMaster = static::where('is_master', true)
                                     ->where('id', '!=', $superAdmin->id)
                                     ->first();

                if ($existingMaster) {
                    throw new \Exception('Only one Master Super Admin is allowed in the system.');
                }
            }
        });

        // Prevent deleting the master admin
        static::deleting(function ($superAdmin) {
            if ($superAdmin->is_master) {
                throw new \Exception('The Master Super Admin cannot be deleted.');
            }
        });
    }

    protected $guard = 'superadmin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'bio',
        'profile_photo_path',
        'is_active',
        'is_master',
        'permissions',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_master' => 'boolean',
        'permissions' => 'array',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'login_attempts' => 'integer',
    ];

    /**
     * Check if super admin has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Master super admin has all permissions
        if ($this->is_master) {
            return true;
        }

        // Check specific permissions
        $permissions = $this->permissions ?? [];

        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    /**
     * Check if super admin has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->is_master) {
            return true;
        }

        $userPermissions = $this->permissions ?? [];

        return !empty(array_intersect($permissions, $userPermissions)) || in_array('*', $userPermissions);
    }

    /**
     * Check if super admin has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->is_master) {
            return true;
        }

        $userPermissions = $this->permissions ?? [];

        return empty(array_diff($permissions, $userPermissions)) || in_array('*', $userPermissions);
    }

    /**
     * Grant permission to super admin
     */
    public function grantPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];

        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => array_unique($permissions)]);
        }
    }

    /**
     * Revoke permission from super admin
     */
    public function revokePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];

        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->update(['permissions' => array_values($permissions)]);
    }

    /**
     * Sync permissions for super admin
     */
    public function syncPermissions(array $permissions): void
    {
        $this->update(['permissions' => array_unique($permissions)]);
    }

    /**
     * Check if account is locked due to failed login attempts
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Increment login attempts and lock account if necessary
     */
    public function incrementLoginAttempts(): void
    {
        $this->increment('login_attempts');

        // Lock account after 5 failed attempts for 15 minutes
        if ($this->login_attempts >= 5) {
            $this->update([
                'locked_until' => Carbon::now()->addMinutes(15),
                'login_attempts' => 0,
            ]);
        }
    }

    /**
     * Reset login attempts on successful login
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => Carbon::now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    /**
     * Get all available permissions in the system
     */
    public static function getAllPermissions(): array
    {
        return [
            // User Management
            'manage_users',
            'manage_admins',
            'manage_super_admins',

            // System Management
            'system_settings',
            'system_backup',
            'system_restore',
            'system_maintenance',

            // Content Management
            'manage_announcements',
            'manage_system_alerts',

            // Reports & Analytics
            'view_reports',
            'view_analytics',
            'export_data',

            // Security & Audit
            'view_audit_logs',
            'manage_security_settings',

            // Financial Management
            'view_financial_reports',
            'manage_payment_settings',

            // API Management
            'manage_apis',
            'view_api_logs',

            // System Monitoring
            'monitor_system_health',
            'view_performance_metrics',

            // Development Tools
            'access_development_tools',
            'manage_database',
        ];
    }

    /**
     * Get default permissions for new super admin
     */
    public static function getDefaultPermissions(): array
    {
        return [
            'manage_users',
            'manage_announcements',
            'view_reports',
            'view_analytics',
        ];
    }

    /**
     * Check if this is the master super admin
     */
    public function isMaster(): bool
    {
        return $this->is_master;
    }

    /**
     * Get formatted last login
     */
    public function getFormattedLastLoginAttribute(): string
    {
        return $this->last_login_at ? $this->last_login_at->format('M d, Y H:i') : 'Never';
    }

    /**
     * Get status badge for display
     */
    public function getStatusBadgeAttribute(): string
    {
        if (!$this->is_active) {
            return '<span class="badge bg-danger">Inactive</span>';
        }

        if ($this->isLocked()) {
            return '<span class="badge bg-warning">Locked</span>';
        }

        return '<span class="badge bg-success">Active</span>';
    }

    /**
     * Scope for active super admins
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for non-locked super admins
     */
    public function scopeUnlocked($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('locked_until')
              ->orWhere('locked_until', '<=', Carbon::now());
        });
    }
}

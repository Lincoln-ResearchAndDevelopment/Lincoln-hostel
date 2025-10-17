<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_admin',
        'is_active',
        'permissions',
        'created_by',
        'updated_by',
        'activated_by',
        'deactivated_by',
        'activated_at',
        'deactivated_at',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'permissions' => 'array',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'login_attempts' => 'integer',
    ];

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->is_admin;
    }

    public function hasPermission($permission)
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array('*', $this->permissions) || in_array($permission, $this->permissions);
    }

    public static function getDefaultAdminPermissions()
    {
        return [
            'rooms.view',
            'rooms.create',
            'rooms.edit',
            'students.view',
            'students.create',
            'students.edit',
            'payments.view',
            'payments.create',
            'payments.edit',
            'complaints.view',
            'complaints.edit',
            'visitors.view',
            'visitors.create',
            'staff.view',
            'reports.view',
        ];
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updated_by_user()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function activated_by_user()
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function deactivated_by_user()
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function incrementLoginAttempts()
    {
        $this->increment('login_attempts');

        if ($this->login_attempts >= 5) {
            $this->update([
                'locked_until' => now()->addMinutes(15),
                'login_attempts' => 0,
            ]);
        }
    }

    public function resetLoginAttempts()
    {
        $this->update([
            'login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Models\LoginLog;

#[Fillable(['tenant_id', 'name', 'email', 'password', 'branch_id', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

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
            'is_active' => 'boolean',
        ];
    }

    // ── Role Check Helpers ────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Check if the user has admin-level access (admin, manager, or owner).
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'manager', 'owner']);
    }

    // ── Scopes ────────────────────────────────────────────────────────

    /**
     * Scope to only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Relationships ─────────────────────────────────────────────────
    public function branch() { return $this->belongsTo(Branch::class); }
    public function sales() { return $this->hasMany(Sale::class); }
    public function loginLogs() { return $this->hasMany(LoginLog::class); }
    public function tenant() { return $this->belongsTo(\App\Models\Tenant::class); }
}


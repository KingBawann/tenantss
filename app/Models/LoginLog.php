<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['tenant_id', 'user_id', 'ip_address', 'user_agent', 'status', 'logged_in_at'])]
class LoginLog extends Model
{
    use BelongsToTenant;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'logged_in_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the login log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function tenant() { return $this->belongsTo(\App\Models\Tenant::class); }
}


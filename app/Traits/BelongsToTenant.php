<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    /**
     * Boot the trait and add the global scope.
     */
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            // Automatically set tenant_id if not already set, and user is authenticated
            if (empty($model->tenant_id) && Auth::check() && Auth::user()->tenant_id) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });
    }

    /**
     * Relationship to the Tenant.
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}

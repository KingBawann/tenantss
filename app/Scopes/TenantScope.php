<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        // Only apply if there is an authenticated user with a tenant_id.
        // This allows console commands or system jobs to run without scoping if needed.
        if (Auth::check() && Auth::user()->tenant_id) {
            $builder->where($model->getTable() . '.tenant_id', Auth::user()->tenant_id);
        }
    }
}

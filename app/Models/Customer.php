<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use BelongsToTenant;


    use SoftDeletes;

    protected $fillable = ['tenant_id', 'name', 'phone', 'balance', 'loyalty_points', 'loyalty_tier'];

    
    public function sales() { return $this->hasMany(Sale::class); }
    public function tenant() { return $this->belongsTo(\App\Models\Tenant::class); }
}


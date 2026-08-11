<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use BelongsToTenant;


    protected $fillable = ['tenant_id', 'name', 'address'];

    
    public function users() { return $this->hasMany(User::class); }
    public function sales() { return $this->hasMany(Sale::class); }
    public function purchases() { return $this->hasMany(Purchase::class); }
    public function tenant() { return $this->belongsTo(\App\Models\Tenant::class); }
}


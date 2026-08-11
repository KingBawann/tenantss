<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use BelongsToTenant;


    use SoftDeletes;

    protected $fillable = ['tenant_id', 'name', 'phone'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function tenant() { return $this->belongsTo(\App\Models\Tenant::class); }
}


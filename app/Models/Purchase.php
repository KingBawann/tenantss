<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{

    protected $fillable = ['tenant_id', 'supplier_id', 'branch_id', 'total', 'date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    
    public function branch() { return $this->belongsTo(Branch::class); }
    public function tenant() { return $this->belongsTo(\App\Models\Tenant::class); }
}


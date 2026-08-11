<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAction extends Model
{
    protected $fillable = ['tenant_id', 'branch_id', 'product_id', 'type', 'quantity', 'date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function tenant() { return $this->belongsTo(\App\Models\Tenant::class); }
}


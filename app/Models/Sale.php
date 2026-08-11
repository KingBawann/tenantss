<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use BelongsToTenant;


    protected $fillable = [
        'tenant_id', 'branch_id', 'user_id', 'customer_id', 
        'total', 'discount_type', 'discount_value', 
        'payment_status', 'payment_method', 'amount_tendered'
    ];

    public function getDiscountedTotalAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return max(0, $this->total - ($this->total * ($this->discount_value / 100)));
        }
        if ($this->discount_type === 'fixed') {
            return max(0, $this->total - $this->discount_value);
        }
        return $this->total;
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    
    public function branch() { return $this->belongsTo(Branch::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function tenant() { return $this->belongsTo(\App\Models\Tenant::class); }
}


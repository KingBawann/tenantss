<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'sale_id', 'user_id', 'reason', 'total'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }
}

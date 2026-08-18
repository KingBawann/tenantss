<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'opening_float',
        'closing_float',
        'expected_cash',
        'cash_variance',
        'status',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opening_float' => 'decimal:2',
        'closing_float' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_variance' => 'decimal:2',
        'opened_at'     => 'datetime',
        'closed_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalSalesAttribute(): float
    {
        return $this->sales()->sum('total');
    }

    public function getCashSalesAttribute(): float
    {
        return $this->sales()->where('payment_method', 'cash')->sum('total');
    }

    public function getCardSalesAttribute(): float
    {
        return $this->sales()->where('payment_method', 'card')->sum('total');
    }

    public function getSalesCountAttribute(): int
    {
        return $this->sales()->count();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}

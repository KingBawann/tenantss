<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BelongsToTenant;


    use SoftDeletes;

    protected $fillable = ['tenant_id', 'category_id', 'name', 'barcode', 'price', 'cost_price', 'stock_quantity'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems() { return $this->hasMany(SaleItem::class); }
    public function purchaseItems() { return $this->hasMany(PurchaseItem::class); }
    public function inventoryActions() { return $this->hasMany(InventoryAction::class); }
    public function tenant() { return $this->belongsTo(\App\Models\Tenant::class); }

    public function getEarliestExpiryDateAttribute()
    {
        // Approximating current batch expiry by looking at recent purchase items
        // Since we don't do strict FIFO batch deduction, we assume very old batches (e.g. >12 months ago)
        // are already sold out and shouldn't trigger global alerts. We'll look at purchases from the last 12 months.
        return $this->purchaseItems()
            ->whereNotNull('expiry_date')
            ->whereHas('purchase', function ($q) {
                $q->where('created_at', '>=', now()->subMonths(12));
            })
            ->orderBy('expiry_date', 'asc')
            ->value('expiry_date');
    }

    public function getExpiryStatusAttribute()
    {
        $expiry = $this->earliest_expiry_date;
        if (!$expiry) {
            return 'good';
        }

        $expiryDate = \Carbon\Carbon::parse($expiry)->startOfDay();
        $now = now()->startOfDay();

        if ($expiryDate->isPast()) {
            return 'expired';
        }

        if ($expiryDate->diffInDays($now) <= 30) {
            return 'expiring_soon';
        }

        return 'good';
    }
}


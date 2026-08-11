<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'quantity' => (int) $this->quantity,
            'cost' => (float) $this->cost,
            'subtotal' => (float) ($this->quantity * $this->cost),
        ];
    }
}

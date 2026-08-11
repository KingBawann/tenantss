<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'total' => (float) $this->total,
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'items' => PurchaseItemResource::collection($this->whenLoaded('items')),
            'tenant_id' => $this->tenant_id,
            'branch_id' => $this->branch_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

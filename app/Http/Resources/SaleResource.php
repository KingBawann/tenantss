<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total' => (float) $this->total,
            'payment_status' => $this->payment_status,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'items' => SaleItemResource::collection($this->whenLoaded('items')),
            'tenant_id' => $this->tenant_id,
            'branch_id' => $this->branch_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

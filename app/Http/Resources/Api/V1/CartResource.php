<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $total = $this->items->sum(fn ($item) => $item->product->price * $item->quantity);

        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->items)->resolve($request),
            'item_count' => $this->items->sum('quantity'),
            'total' => $total,
            'formatted_total' => 'Rp '.number_format($total, 0, ',', '.'),
        ];
    }
}

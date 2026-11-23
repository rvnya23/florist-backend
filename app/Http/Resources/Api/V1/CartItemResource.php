<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subtotal = $this->product->price * $this->quantity;

        return [
            'id' => $this->id,
            'product' => (new ProductResource($this->product))->resolve($request),
            'quantity' => $this->quantity,
            'subtotal' => $subtotal,
            'formatted_subtotal' => 'Rp '.number_format($subtotal, 0, ',', '.'),
        ];
    }
}

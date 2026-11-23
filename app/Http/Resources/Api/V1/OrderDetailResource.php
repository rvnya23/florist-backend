<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => (new ProductResource($this->product))->resolve($request),
            'harga_satuan' => $this->harga_satuan,
            'formatted_harga_satuan' => 'Rp '.number_format($this->harga_satuan, 0, ',', '.'),
            'jumlah' => $this->jumlah,
            'subtotal' => $this->subtotal,
            'formatted_subtotal' => 'Rp '.number_format($this->subtotal, 0, ',', '.'),
        ];
    }
}

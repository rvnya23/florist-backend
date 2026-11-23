<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'order_date' => $this->order_date?->toISOString(),
            'total_harga' => $this->total_harga,
            'formatted_total_harga' => 'Rp '.number_format($this->total_harga, 0, ',', '.'),
            'alamat_pengiriman' => $this->alamat_pengiriman,
            'catatan' => $this->catatan,
            'status' => $this->status,
            'member' => new MemberResource($this->whenLoaded('member')),
            'details' => OrderDetailResource::collection($this->whenLoaded('details')),
            'payment' => $this->when(
                $this->relationLoaded('payment') && $this->payment,
                fn () => (new TransaksiOrderResource($this->payment))->resolve($request),
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

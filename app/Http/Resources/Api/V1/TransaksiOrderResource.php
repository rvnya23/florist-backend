<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TransaksiOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_pembayaran' => $this->kode_pembayaran,
            'rekening_bank' => new RekeningBankResource($this->whenLoaded('rekeningBank')),
            'nama_pengirim' => $this->nama_pengirim,
            'tanggal_pembayaran' => $this->tanggal_pembayaran?->toDateString(),
            'jumlah_pembayaran' => $this->jumlah_pembayaran,
            'formatted_jumlah_pembayaran' => 'Rp '.number_format($this->jumlah_pembayaran, 0, ',', '.'),
            'bukti_pembayaran_url' => url(Storage::disk('public')->url($this->bukti_pembayaran)),
            'status' => $this->status,
            'verifier' => new UserResource($this->whenLoaded('verifier')),
            'verified_at' => $this->verified_at?->toISOString(),
            'catatan_penolakan' => $this->catatan_penolakan,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

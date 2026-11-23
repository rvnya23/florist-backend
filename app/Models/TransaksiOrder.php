<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'rekening_bank_id',
    'kode_pembayaran',
    'nama_pengirim',
    'tanggal_pembayaran',
    'jumlah_pembayaran',
    'bukti_pembayaran',
    'status',
    'verified_by',
    'verified_at',
    'catatan_penolakan',
])]
class TransaksiOrder extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    protected $table = 'transaksi_order';

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function rekeningBank(): BelongsTo
    {
        return $this->belongsTo(RekeningBank::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected function casts(): array
    {
        return [
            'tanggal_pembayaran' => 'date',
            'verified_at' => 'datetime',
        ];
    }
}

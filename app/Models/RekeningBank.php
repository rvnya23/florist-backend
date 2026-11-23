<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_bank', 'nomor_rekening', 'nama_pemilik', 'is_active'])]
class RekeningBank extends Model
{
    protected $table = 'rekening_bank';

    public function transaksiOrders(): HasMany
    {
        return $this->hasMany(TransaksiOrder::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}

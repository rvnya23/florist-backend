<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['member_id', 'order_code', 'order_date', 'total_harga', 'alamat_pengiriman', 'catatan', 'status'])]
class Order extends Model
{
    public const STATUS_PENDING_PAYMENT = 'pending_payment';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(TransaksiOrder::class);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_CONFIRMED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    protected function casts(): array
    {
        return ['order_date' => 'datetime'];
    }
}

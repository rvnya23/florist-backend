<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'price', 'description', 'image'])]
class Product extends Model
{
    /**
     * Get the formatted price in Rupiah.
     */
    public function getFormattedPrice(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}

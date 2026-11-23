<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

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
}

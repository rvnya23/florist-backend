<?php

namespace App\Actions;

use App\Models\Cart;
use App\Models\Member;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutCartAction
{
    public function execute(Member $member, array $data): Order
    {
        return DB::transaction(function () use ($member, $data): Order {
            $cart = Cart::query()
                ->whereBelongsTo($member)
                ->lockForUpdate()
                ->first();

            if (! $cart) {
                throw ValidationException::withMessages(['cart' => ['Keranjang masih kosong.']]);
            }

            $items = $cart->items()
                ->with('product')
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => ['Keranjang masih kosong.']]);
            }

            $details = $items->map(function ($item): array {
                $subtotal = $item->product->price * $item->quantity;

                return [
                    'product_id' => $item->product_id,
                    'harga_satuan' => $item->product->price,
                    'jumlah' => $item->quantity,
                    'subtotal' => $subtotal,
                ];
            });

            $order = Order::query()->create([
                'member_id' => $member->id,
                'order_code' => 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'order_date' => now(),
                'total_harga' => $details->sum('subtotal'),
                'alamat_pengiriman' => $data['alamat_pengiriman'],
                'catatan' => $data['catatan'] ?? null,
                'status' => Order::STATUS_PENDING_PAYMENT,
            ]);

            $order->details()->createMany($details->all());
            $cart->items()->delete();

            return $order->load(['member', 'details.product', 'payment']);
        });
    }
}

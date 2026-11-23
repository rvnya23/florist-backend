<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCartItemRequest;
use App\Http\Requests\Api\V1\UpdateCartItemRequest;
use App\Http\Resources\Api\V1\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return APIResponse::success($this->resource($request));
    }

    public function store(StoreCartItemRequest $request): JsonResponse
    {
        $member = $request->user()->member;
        $data = $request->validated();

        DB::transaction(function () use ($member, $data): void {
            $cart = Cart::query()->firstOrCreate(['member_id' => $member->id]);
            $item = $cart->items()
                ->where('product_id', $data['product_id'])
                ->lockForUpdate()
                ->first();
            $quantity = ($item?->quantity ?? 0) + ($data['quantity'] ?? 1);

            if ($quantity > 99) {
                throw ValidationException::withMessages([
                    'quantity' => ['Jumlah produk dalam keranjang maksimal 99.'],
                ]);
            }

            $cart->items()->updateOrCreate(
                ['product_id' => $data['product_id']],
                ['quantity' => $quantity],
            );
        });

        return APIResponse::success($this->resource($request), 'Produk ditambahkan ke keranjang.', 201);
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $this->ensureOwner($request, $cartItem);
        $cartItem->update($request->validated());

        return APIResponse::success($this->resource($request), 'Jumlah produk diperbarui.');
    }

    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        $this->ensureOwner($request, $cartItem);
        $cartItem->delete();

        return APIResponse::success($this->resource($request), 'Produk dihapus dari keranjang.');
    }

    private function resource(Request $request): array
    {
        $cart = Cart::query()->firstOrCreate(['member_id' => $request->user()->member->id]);
        $cart->load('items.product');

        return (new CartResource($cart))->resolve($request);
    }

    private function ensureOwner(Request $request, CartItem $cartItem): void
    {
        abort_unless($cartItem->cart->member_id === $request->user()->member->id, 404);
    }
}

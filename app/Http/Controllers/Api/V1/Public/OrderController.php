<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Actions\CheckoutCartAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckoutRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->whereBelongsTo($request->user()->member)
            ->with(['details.product', 'payment.rekeningBank', 'payment.verifier'])
            ->latest('order_date')
            ->get();

        return APIResponse::success(OrderResource::collection($orders)->resolve($request));
    }

    public function store(CheckoutRequest $request, CheckoutCartAction $checkout): JsonResponse
    {
        $order = $checkout->execute($request->user()->member, $request->validated());

        return APIResponse::success(
            (new OrderResource($order))->resolve($request),
            'Order berhasil dibuat.',
            201,
        );
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->ensureOwner($request, $order);
        $order->load(['member', 'details.product', 'payment.rekeningBank', 'payment.verifier']);

        return APIResponse::success((new OrderResource($order))->resolve($request));
    }

    private function ensureOwner(Request $request, Order $order): void
    {
        abort_unless($order->member_id === $request->user()->member->id, 404);
    }
}

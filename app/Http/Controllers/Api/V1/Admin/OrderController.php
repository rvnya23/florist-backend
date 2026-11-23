<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminOrderIndexRequest;
use App\Http\Requests\Api\V1\UpdateOrderStatusRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(AdminOrderIndexRequest $request): JsonResponse
    {
        $orders = Order::query()
            ->with(['member', 'details.product', 'payment.rekeningBank'])
            ->when($request->validated('status'), fn ($query, $status) => $query->where('status', $status))
            ->when(
                $request->validated('payment_status'),
                fn ($query, $status) => $query->whereHas('payment', fn ($payment) => $payment->where('status', $status)),
            )
            ->latest('order_date')
            ->paginate(15);

        return APIResponse::paginated($orders, OrderResource::class);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $order->load(['member', 'details.product', 'payment.rekeningBank', 'payment.verifier']);

        return APIResponse::success((new OrderResource($order))->resolve($request));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $nextStatus = $request->validated('status');
        $allowed = match ($order->status) {
            Order::STATUS_PENDING_PAYMENT => [Order::STATUS_CANCELLED],
            Order::STATUS_CONFIRMED => [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED],
            default => [],
        };

        if (! in_array($nextStatus, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => ['Perubahan status order tidak diperbolehkan.'],
            ]);
        }

        $order->update(['status' => $nextStatus]);

        return APIResponse::success(
            (new OrderResource($order->load(['member', 'details.product', 'payment.rekeningBank'])))->resolve($request),
            'Status order berhasil diperbarui.',
        );
    }
}

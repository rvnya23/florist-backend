<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\SubmitOrderPaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrderPaymentRequest;
use App\Http\Requests\Api\V1\RejectOrderPaymentRequest;
use App\Http\Resources\Api\V1\TransaksiOrderResource;
use App\Models\Order;
use App\Models\TransaksiOrder;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderPaymentController extends Controller
{
    public function store(
        OrderPaymentRequest $request,
        Order $order,
        SubmitOrderPaymentAction $submitPayment,
    ): JsonResponse {
        $payment = $submitPayment->execute(
            $order,
            $request->validated(),
            $request->file('bukti_pembayaran'),
        );

        return APIResponse::success(
            (new TransaksiOrderResource($payment))->resolve($request),
            'Pembayaran berhasil ditambahkan.',
            201,
        );
    }

    public function verify(Request $request, Order $order): JsonResponse
    {
        $payment = DB::transaction(function () use ($request, $order): TransaksiOrder {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $payment = TransaksiOrder::query()->whereBelongsTo($lockedOrder)->lockForUpdate()->first();

            if (
                $lockedOrder->status !== Order::STATUS_PENDING_PAYMENT
                || ! $payment
                || $payment->status !== TransaksiOrder::STATUS_PENDING
            ) {
                throw ValidationException::withMessages([
                    'payment' => ['Pembayaran yang menunggu verifikasi tidak ditemukan.'],
                ]);
            }

            $payment->update([
                'status' => TransaksiOrder::STATUS_VERIFIED,
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
                'catatan_penolakan' => null,
            ]);
            $lockedOrder->update(['status' => Order::STATUS_CONFIRMED]);

            return $payment;
        });

        return APIResponse::success(
            (new TransaksiOrderResource($payment->load(['rekeningBank', 'verifier'])))->resolve($request),
            'Pembayaran berhasil diverifikasi.',
        );
    }

    public function reject(RejectOrderPaymentRequest $request, Order $order): JsonResponse
    {
        $payment = DB::transaction(function () use ($request, $order): TransaksiOrder {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $payment = TransaksiOrder::query()->whereBelongsTo($lockedOrder)->lockForUpdate()->first();

            if (
                $lockedOrder->status !== Order::STATUS_PENDING_PAYMENT
                || ! $payment
                || $payment->status !== TransaksiOrder::STATUS_PENDING
            ) {
                throw ValidationException::withMessages([
                    'payment' => ['Pembayaran yang menunggu verifikasi tidak ditemukan.'],
                ]);
            }

            $payment->update([
                'status' => TransaksiOrder::STATUS_REJECTED,
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
                'catatan_penolakan' => $request->validated('catatan_penolakan'),
            ]);

            return $payment;
        });

        return APIResponse::success(
            (new TransaksiOrderResource($payment->load(['rekeningBank', 'verifier'])))->resolve($request),
            'Pembayaran ditolak.',
        );
    }
}

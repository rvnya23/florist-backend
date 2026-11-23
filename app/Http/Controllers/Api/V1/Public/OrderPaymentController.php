<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Actions\SubmitOrderPaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrderPaymentRequest;
use App\Http\Resources\Api\V1\TransaksiOrderResource;
use App\Models\Order;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;

class OrderPaymentController extends Controller
{
    public function store(
        OrderPaymentRequest $request,
        Order $order,
        SubmitOrderPaymentAction $submitPayment,
    ): JsonResponse {
        abort_unless($order->member_id === $request->user()->member->id, 404);

        $payment = $submitPayment->execute(
            $order,
            $request->validated(),
            $request->file('bukti_pembayaran'),
        );

        return APIResponse::success(
            (new TransaksiOrderResource($payment))->resolve($request),
            'Bukti pembayaran berhasil dikirim.',
            201,
        );
    }
}

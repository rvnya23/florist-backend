<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\TransaksiOrder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class SubmitOrderPaymentAction
{
    public function execute(Order $order, array $data, UploadedFile $proof): TransaksiOrder
    {
        $newProof = $proof->store('payments', 'public');
        $oldProof = null;

        try {
            $payment = DB::transaction(function () use ($order, $data, $newProof, &$oldProof): TransaksiOrder {
                $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

                if ($lockedOrder->status !== Order::STATUS_PENDING_PAYMENT) {
                    throw ValidationException::withMessages([
                        'order' => ['Pembayaran hanya dapat dikirim untuk order yang menunggu pembayaran.'],
                    ]);
                }

                if ((int) $data['jumlah_pembayaran'] !== $lockedOrder->total_harga) {
                    throw ValidationException::withMessages([
                        'jumlah_pembayaran' => ['Jumlah pembayaran harus sama dengan total order.'],
                    ]);
                }

                $existing = TransaksiOrder::query()
                    ->whereBelongsTo($lockedOrder)
                    ->lockForUpdate()
                    ->first();

                if ($existing && $existing->status !== TransaksiOrder::STATUS_REJECTED) {
                    throw ValidationException::withMessages([
                        'payment' => ['Bukti pembayaran untuk order ini sedang diproses atau sudah diterima.'],
                    ]);
                }

                $values = [
                    'rekening_bank_id' => $data['rekening_bank_id'],
                    'nama_pengirim' => $data['nama_pengirim'],
                    'tanggal_pembayaran' => $data['tanggal_pembayaran'] ?? today(),
                    'jumlah_pembayaran' => $data['jumlah_pembayaran'],
                    'bukti_pembayaran' => $newProof,
                    'status' => TransaksiOrder::STATUS_PENDING,
                    'verified_by' => null,
                    'verified_at' => null,
                    'catatan_penolakan' => null,
                ];

                if ($existing) {
                    $oldProof = $existing->bukti_pembayaran;
                    $existing->update($values);

                    return $existing;
                }

                return $lockedOrder->payment()->create([
                    ...$values,
                    'kode_pembayaran' => 'PAY-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                ]);
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newProof);
            throw $exception;
        }

        if ($oldProof) {
            Storage::disk('public')->delete($oldProof);
        }

        return $payment->load(['rekeningBank', 'verifier']);
    }
}

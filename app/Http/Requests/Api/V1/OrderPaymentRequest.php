<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rekening_bank_id' => [
                'required',
                'integer',
                Rule::exists('rekening_bank', 'id')->where('is_active', true),
            ],
            'nama_pengirim' => ['required', 'string', 'max:255'],
            'tanggal_pembayaran' => ['nullable', 'date', 'before_or_equal:today'],
            'jumlah_pembayaran' => ['required', 'integer', 'min:1'],
            'bukti_pembayaran' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}

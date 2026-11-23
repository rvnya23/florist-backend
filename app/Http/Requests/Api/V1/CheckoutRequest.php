<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alamat_pengiriman' => ['required', 'string', 'max:1000'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

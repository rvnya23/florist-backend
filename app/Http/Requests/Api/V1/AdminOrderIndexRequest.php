<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Order;
use App\Models\TransaksiOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminOrderIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(Order::statuses())],
            'payment_status' => ['nullable', Rule::in([
                TransaksiOrder::STATUS_PENDING,
                TransaksiOrder::STATUS_VERIFIED,
                TransaksiOrder::STATUS_REJECTED,
            ])],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}

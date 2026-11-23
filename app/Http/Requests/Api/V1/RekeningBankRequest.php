<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RekeningBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_bank' => ['required', 'string', 'max:255'],
            'nomor_rekening' => [
                'required',
                'string',
                'max:100',
                Rule::unique('rekening_bank', 'nomor_rekening')->ignore($this->route('bankAccount')),
            ],
            'nama_pemilik' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

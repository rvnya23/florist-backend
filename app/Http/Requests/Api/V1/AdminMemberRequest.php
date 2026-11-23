<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('members', 'email')->ignore($this->route('member')),
            ],
            'phone' => [
                'required',
                'string',
                'max:255',
                Rule::unique('members', 'phone')->ignore($this->route('member')),
            ],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}

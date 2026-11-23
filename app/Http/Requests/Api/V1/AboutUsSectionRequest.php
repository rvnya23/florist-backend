<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AboutUsSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('about_us_sections', 'section_key')->ignore($this->route('aboutUsSection')),
            ],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }
}

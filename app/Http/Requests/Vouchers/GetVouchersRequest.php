<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class GetVouchersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['required', 'int', 'gt:0'],
            'paginate' => ['required', 'int', 'gt:0'],
            'serie' => 'nullable|string',
            'correlative' => 'nullable|string',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date'
        ];
    }
}

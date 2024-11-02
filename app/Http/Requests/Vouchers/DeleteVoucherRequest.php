<?php

namespace App\Http\Requests\Vouchers;

use Illuminate\Foundation\Http\FormRequest;

class DeleteVoucherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:vouchers,id'],
        ];
    }
}

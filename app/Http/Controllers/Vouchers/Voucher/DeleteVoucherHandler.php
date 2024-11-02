<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Http\Requests\Vouchers\DeleteVoucherRequest;
use App\Services\VoucherService;

class DeleteVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
        //
    }

    public function __invoke(DeleteVoucherRequest $request)
    {
        $this->voucherService->delete($request->get('id'));

        return response([
            'message' => 'Voucher deleted successfully',
        ], 200);
    }
}

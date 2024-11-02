<?php

namespace App\Http\Controllers\Vouchers;

use App\Services\VoucherService;
use Illuminate\Http\Request;

class SummaryVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {

    }

    public function __invoke(Request $request)
    {
        try {
            $summary = $this->voucherService->summaryAmountVouchers();

            return response([
                'data' => $summary,
            ], 202);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}

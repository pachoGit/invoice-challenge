<?php

use App\Http\Controllers\Vouchers\GetVouchersHandler;
use App\Http\Controllers\Vouchers\StoreVouchersHandler;
use App\Http\Controllers\Vouchers\SummaryVouchersHandler;
use App\Http\Controllers\Vouchers\TestVoucherHandler;
use App\Http\Controllers\Vouchers\Voucher\DeleteVoucherHandler;
use App\Http\Controllers\Vouchers\Voucher\GetVoucherHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('vouchers')->group(
    function () {
        Route::get('/', GetVouchersHandler::class);
        Route::post('/', StoreVouchersHandler::class);
        Route::get('/test', TestVoucherHandler::class);
        Route::get('/summary', SummaryVouchersHandler::class);
        Route::delete('/', DeleteVoucherHandler::class);
    }
);

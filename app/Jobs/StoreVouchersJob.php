<?php

namespace App\Jobs;

use App\Services\VoucherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StoreVouchersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $xmlContents, private VoucherService $voucherService)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = auth()->user();
        $vouchers = $this->voucherService->storeVouchersFromXmlContents($this->xmlContents, $user);
    }
}

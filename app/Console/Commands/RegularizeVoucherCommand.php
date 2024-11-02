<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Console\Command;

class RegularizeVoucherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voucher:regularize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regularize voucher data.';

    /**
     * Execute the console command.
     */
    public function handle(VoucherService $voucherService)
    {
        $query = Voucher::query();
        $query->where('serie', null)
            ->orWhere('correlative', null)
            ->orWhere('type_voucher', null)
            ->orWhere('type_currency', null);
        $vouchers = $query->get();

        try
        {
            $result = $voucherService->regularizeVouchers($vouchers);
        }
        catch (\Exception $e)
        {
            $this->error('Error regularizing vouchers: ' . $e->getMessage());
            return;
        }

        $this->info('Regularizing vouchers: ' . $result);
    }
}

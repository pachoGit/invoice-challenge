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
    protected $signature = 'voucher:regularize {userEmail}';

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

        $userEmail = $this->argument('userEmail');

        $query->with('user');

        $query->whereHas('user', function ($query) use ($userEmail) {
            $query->where('email', '=', $userEmail);
        })->where(function ($query) {
            $query->where('serie', null)
                ->orWhere('correlative', null)
                ->orWhere('type_voucher', null)
                ->orWhere('type_currency', null);
        });

        $vouchers = $query->get();

        try
        {
            $result = $voucherService->regularizeVouchers($vouchers);
            $this->info('Regularizing vouchers: ' . $result);
        }
        catch (\Exception $e)
        {
            $this->error('Error regularizing vouchers: ' . $e->getMessage());
        }
    }
}

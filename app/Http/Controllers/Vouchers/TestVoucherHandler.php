<?php

namespace App\Http\Controllers\Vouchers;

use App\Models\Voucher;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class TestVoucherHandler
{
    public function __construct()
    {

    }

    public function __invoke()
    {
        $voucher = Voucher::first();
        Log::info('Voucher: ' . $voucher->id);

        try
        {
            $xml = new SimpleXMLElement($voucher->xml_content);
            $numeration = (string) $xml->xpath('//cbc:ID')[0];
            $typeCode = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
            $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

            Log::info('XML: ' . json_encode($currency));
        }
        catch (\Exception $e)
        {
            Log::error('Error: ' . $e->getMessage());
        }
    }
}

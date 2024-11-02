<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(int $page, int $paginate): LengthAwarePaginator
    {
        return Voucher::with(['lines', 'user'])->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        foreach ($xmlContents as $xmlContent) {
            $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
        }

        VouchersCreated::dispatch($vouchers, $user);

        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);

        $issuerName = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0];
        $issuerDocumentType = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $issuerDocumentNumber = (string) $xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $receiverName = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0];
        $receiverDocumentType = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0];
        $receiverDocumentNumber = (string) $xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0];

        $totalAmount = (string) $xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0];

        $voucher = new Voucher([
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) $invoiceLine->xpath('cac:Item/cbc:Description')[0];
            $quantity = (float) $invoiceLine->xpath('cbc:InvoicedQuantity')[0];
            $unitPrice = (float) $invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0];

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }


    /**
     *
     * @param Collection $voucher
     *
     * @return int
     */
    public function regularizeVouchers(Collection $vouchers): int
    {
        $upsert = [];
        try
        {
            foreach ($vouchers as $voucher)
            {
                $xml = new SimpleXMLElement($voucher->xml_content);

                $numeration = (string) $xml->xpath('//cbc:ID')[0];
                $typevoucher = (string) $xml->xpath('//cbc:InvoiceTypeCode')[0];
                $currency = (string) $xml->xpath('//cbc:DocumentCurrencyCode')[0];

                [$serie, $correlative] = explode('-', $numeration);

                $upsert[] = [
                    'id' => $voucher->id,
                    'serie' => $serie,
                    'correlative' => $correlative,
                    'type_voucher' => $typevoucher,
                    'type_currency' => $currency,
                    'issuer_name' => $voucher->issuer_name,
                    'issuer_document_type' => $voucher->issuer_document_type,
                    'issuer_document_number' => $voucher->issuer_document_number,
                    'receiver_name' => $voucher->receiver_name,
                    'receiver_document_type' => $voucher->receiver_document_type,
                    'receiver_document_number' => $voucher->receiver_document_number,
                    'total_amount' => $voucher->total_amount,
                    'xml_content' => $voucher->xml_content,
                    'user_id' => $voucher->user_id,
                ];
            }

            Voucher::upsert($upsert, ['id'], ['serie', 'correlative', 'type_voucher', 'type_currency']);
        }
        catch (\Exception $exception)
        {
            throw $exception;
        }

        return count($upsert);
    }


    public function summaryAmountVouchers()
    {
        $PEN_TO_USD = 0.27;

        $totalSum = VoucherLine::join('vouchers', 'vouchers.id', '=', 'voucher_lines.voucher_id')
            ->where('vouchers.user_id', auth()->user()->getAuthIdentifier())
            ->selectRaw('SUM(voucher_lines.quantity * voucher_lines.unit_price) as total_sum')
            ->value('total_sum');

        // NOTE: Simple conversion
        return [
            'pen' => (double) $totalSum,
            'usd' => (double) $totalSum * $PEN_TO_USD,
        ];
    }

    public function delete(string $id)
    {
        return Voucher::where('id', $id)->delete();
    }
}

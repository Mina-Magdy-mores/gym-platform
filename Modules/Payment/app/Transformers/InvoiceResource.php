<?php

namespace Modules\Payment\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the invoice data resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'invoice_number' => $this['invoice_number'],
            'item_description' => $this['item_description'],
            'subtotal' => (float) $this['subtotal'],
            'vat_rate' => $this['vat_rate'],
            'vat_amount' => (float) $this['vat_amount'],
            'total_amount' => (float) $this['total_amount'],
            'currency' => $this['currency'],
            'tax_registration_no' => $this['tax_registration_no'],
            'download_url' => route('api.v1.invoices.download', $this['payment']->id),
            'verification_url' => $this['verification_url'],
        ];
    }
}

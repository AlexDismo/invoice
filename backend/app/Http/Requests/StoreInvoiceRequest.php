<?php

namespace App\Http\Requests;

use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'max:255', 'unique:invoices,number'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_tax_id' => ['required', 'string', 'max:255'],
            'net_amount' => ['required', 'numeric', 'gt:0'],
            'vat_amount' => ['required', 'numeric', 'gte:0'],
            'gross_amount' => ['required', 'numeric'],
            'currency' => ['required', 'string', 'size:3'],
            'status' => ['sometimes', Rule::enum(InvoiceStatus::class)],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $net = number_format((float) $this->input('net_amount'), 2, '.', '');
            $vat = number_format((float) $this->input('vat_amount'), 2, '.', '');
            $gross = number_format((float) $this->input('gross_amount'), 2, '.', '');
            $expected = bcadd($net, $vat, 2);

            if ($gross !== $expected) {
                $validator->errors()->add('gross_amount', 'Gross amount must equal net amount plus VAT amount.');
            }
        });
    }
}

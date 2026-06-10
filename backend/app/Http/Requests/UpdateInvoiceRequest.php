<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('invoice')->isEditable();
    }

    public function rules(): array
    {
        $issueDate = $this->route('invoice')->issue_date->toDateString();

        return [
            'net_amount' => ['required', 'numeric', 'gt:0'],
            'vat_amount' => ['required', 'numeric', 'gte:0'],
            'due_date' => ['required', 'date', 'after_or_equal:'.$issueDate],
        ];
    }

    protected function failedAuthorization(): void
    {
        abort(422, 'Only pending invoices can be updated.');
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $net = number_format((float) $this->input('net_amount'), 2, '.', '');
            $vat = number_format((float) $this->input('vat_amount'), 2, '.', '');
            $expectedGross = bcadd($net, $vat, 2);

            $this->merge(['gross_amount' => $expectedGross]);
        });
    }
}

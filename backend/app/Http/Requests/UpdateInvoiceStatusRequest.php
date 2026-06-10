<?php

namespace App\Http\Requests;

use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('invoice')->status === InvoiceStatus::Pending;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(InvoiceStatus::class)],
        ];
    }

    protected function failedAuthorization(): void
    {
        abort(422, 'Only pending invoices can be approved or rejected.');
    }
}

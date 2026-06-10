<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        if (Invoice::query()->exists()) {
            return;
        }

        $invoices = [
            [
                'number' => 'INV-2026-001',
                'supplier_name' => 'ТОВ «Сонячний Край»',
                'supplier_tax_id' => '1234567890',
                'net_amount' => '10000.00',
                'vat_amount' => '2000.00',
                'gross_amount' => '12000.00',
                'currency' => 'UAH',
                'status' => InvoiceStatus::Pending,
                'issue_date' => '2026-05-01',
                'due_date' => '2026-05-31',
            ],
            [
                'number' => 'INV-2026-002',
                'supplier_name' => 'ПП «Будматеріали+»',
                'supplier_tax_id' => '9876543210',
                'net_amount' => '5500.50',
                'vat_amount' => '1100.10',
                'gross_amount' => '6600.60',
                'currency' => 'UAH',
                'status' => InvoiceStatus::Approved,
                'issue_date' => '2026-04-15',
                'due_date' => '2026-05-15',
            ],
            [
                'number' => 'INV-2026-003',
                'supplier_name' => 'ФОП Іваненко О.М.',
                'supplier_tax_id' => '1122334455',
                'net_amount' => '3200.00',
                'vat_amount' => '0.00',
                'gross_amount' => '3200.00',
                'currency' => 'UAH',
                'status' => InvoiceStatus::Rejected,
                'issue_date' => '2026-03-10',
                'due_date' => '2026-04-10',
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoice::query()->create($invoice);
        }
    }
}

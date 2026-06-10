export type InvoiceStatus = 'pending' | 'approved' | 'rejected'

export interface Invoice {
  id: string
  number: string
  supplier_name: string
  supplier_tax_id: string
  net_amount: string
  vat_amount: string
  gross_amount: string
  currency: string
  status: InvoiceStatus
  issue_date: string
  due_date: string
  created_at: string
  updated_at: string
}

export interface InvoiceCreatePayload {
  number: string
  supplier_name: string
  supplier_tax_id: string
  net_amount: number
  vat_amount: number
  gross_amount: number
  currency: string
  issue_date: string
  due_date: string
}

export interface InvoiceEditPayload {
  net_amount: number
  vat_amount: number
  due_date: string
}

export interface InvoiceStatusPayload {
  status: 'approved' | 'rejected'
}

export interface ApiValidationError {
  message: string
  errors?: Record<string, string[]>
}

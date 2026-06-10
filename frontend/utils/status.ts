import type { InvoiceStatus } from '~/types/invoice'

export const statusLabels: Record<InvoiceStatus, string> = {
  pending: 'Pending',
  approved: 'Approved',
  rejected: 'Rejected',
}

export const statusTagTypes: Record<InvoiceStatus, 'warning' | 'success' | 'danger'> = {
  pending: 'warning',
  approved: 'success',
  rejected: 'danger',
}

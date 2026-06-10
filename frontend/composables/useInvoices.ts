import type { Invoice, InvoiceCreatePayload, InvoiceEditPayload, InvoiceStatusPayload } from '~/types/invoice'

export function useInvoices() {
  const config = useRuntimeConfig()

  const baseUrl = import.meta.server ? config.apiBase : config.public.apiBase

  const list = () =>
    $fetch<{ data: Invoice[] }>(`${baseUrl}/invoices`).then((response) => response.data)

  const get = (id: string) =>
    $fetch<{ data: Invoice }>(`${baseUrl}/invoices/${id}`).then((response) => response.data)

  const create = (payload: InvoiceCreatePayload) =>
    $fetch<{ data: Invoice }>(`${baseUrl}/invoices`, {
      method: 'POST',
      body: payload,
    }).then((response) => response.data)

  const update = (id: string, payload: InvoiceEditPayload) =>
    $fetch<{ data: Invoice }>(`${baseUrl}/invoices/${id}`, {
      method: 'PUT',
      body: payload,
    }).then((response) => response.data)

  const changeStatus = (id: string, payload: InvoiceStatusPayload) =>
    $fetch<{ data: Invoice }>(`${baseUrl}/invoices/${id}/status`, {
      method: 'PATCH',
      body: payload,
    }).then((response) => response.data)

  return { list, get, create, update, changeStatus }
}

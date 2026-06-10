export function formatMoney(amount: string | number, currency: string): string {
  const formatted = new Intl.NumberFormat('uk-UA', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(Number(amount))

  return `${formatted} ${currency}`
}

export function formatDate(date: string): string {
  return new Intl.DateTimeFormat('uk-UA', {
    dateStyle: 'long',
    timeZone: 'UTC',
  }).format(new Date(date))
}

export function formatDateTime(date: string): string {
  return new Intl.DateTimeFormat('uk-UA', {
    dateStyle: 'medium',
    timeStyle: 'short',
    timeZone: 'UTC',
  }).format(new Date(date))
}

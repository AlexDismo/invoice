import type { ApiValidationError } from '~/types/invoice'

export function applyApiFieldErrors(
  apiError: ApiValidationError | undefined,
  setFieldError: (field: string, message: string | string[] | undefined) => void,
): boolean {
  if (!apiError?.errors) {
    return false
  }

  Object.entries(apiError.errors).forEach(([field, messages]) => {
    setFieldError(field, messages[0])
  })

  return true
}

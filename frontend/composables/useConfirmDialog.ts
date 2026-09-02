interface ConfirmOptions {
  title?: string
  confirmText?: string
  cancelText?: string
  danger?: boolean
}

interface ConfirmRequest extends ConfirmOptions {
  message: string
  resolve: (value: boolean) => void
}

export function useConfirmDialog() {
  const request = useState<ConfirmRequest | null>('sj-confirm-request', () => null)

  const confirmDialog = (message: string, options: ConfirmOptions = {}) => {
    return new Promise<boolean>((resolve) => {
      request.value = { message, resolve, ...options }
    })
  }

  const resolveConfirm = (value: boolean) => {
    request.value?.resolve(value)
    request.value = null
  }

  return { request, confirmDialog, resolveConfirm }
}

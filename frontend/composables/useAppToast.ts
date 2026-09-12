export interface AppToastItem {
  id: number
  type: 'success' | 'error' | 'info'
  message: string
}

let counter = 0

export function useAppToast() {
  const toasts = useState<AppToastItem[]>('sj-app-toasts', () => [])

  const remove = (id: number) => {
    toasts.value = toasts.value.filter((t) => t.id !== id)
  }

  const push = (type: AppToastItem['type'], message: string, duration = 4000) => {
    const id = ++counter
    toasts.value = [...toasts.value, { id, type, message }]
    if (import.meta.client) {
      setTimeout(() => remove(id), duration)
    }
    return id
  }

  return {
    toasts,
    success: (message: string, duration?: number) => push('success', message, duration),
    error: (message: string, duration?: number) => push('error', message, duration),
    info: (message: string, duration?: number) => push('info', message, duration),
    remove,
  }
}

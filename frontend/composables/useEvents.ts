export type SchoolEvent = {
  id: number | string
  title: string
  description?: string
  date?: string
  time?: string
  location?: string
  type?: string
  status?: string
  organizer?: string
  youtubeLink?: string
  images?: string[]
  image?: string
  createdAt?: string
  [key: string]: unknown
}

export function useEvents() {
  const { apiFetch, mediaUrl, applyStoredI18n } = useApi()

  const localizeEvent = (event: SchoolEvent): SchoolEvent =>
    applyStoredI18n(event, ['title', 'description', 'location'])

  const events = ref<SchoolEvent[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const imageIndices = ref<Record<string | number, number>>({})

  const initImageIndices = (list: SchoolEvent[]) => {
    list.forEach((event) => {
      if (event.images && event.images.length > 1) {
        imageIndices.value[event.id] = imageIndices.value[event.id] ?? 0
      }
    })
  }

  const fetchEvents = async (params?: { page?: number; limit?: number; silent?: boolean }) => {
    const silent = Boolean(params?.silent)
    if (!silent) {
      isLoading.value = true
    }
    error.value = null

    try {
      const query: Record<string, number> = {}
      if (params?.page) query.page = params.page
      if (params?.limit) query.limit = params.limit

      const res = await apiFetch<{ success: boolean; data: SchoolEvent[] }>(
        '/api/events',
        Object.keys(query).length ? { query } : undefined
      )
      events.value = (Array.isArray(res.data) ? res.data : []).map(localizeEvent)
      initImageIndices(events.value)
      return events.value
    } catch (err: any) {
      error.value = err?.message || 'Failed to fetch events'
      if (!silent) {
        events.value = []
      }
      return events.value
    } finally {
      if (!silent) {
        isLoading.value = false
      }
    }
  }

  const fetchEventById = async (id: string | number) => {
    isLoading.value = true
    error.value = null

    try {
      const res = await apiFetch<{ success: boolean; data: SchoolEvent }>(
        '/api/events',
        { query: { id } }
      )
      if (res?.data) {
        const event = localizeEvent(res.data)
        initImageIndices([event])
        return event
      }
      return null
    } catch (err: any) {
      // Fallback: list endpoint (older deployments without get-event.php CORS)
      try {
        const list = await fetchEvents()
        return list.find((e) => String(e.id) === String(id)) || null
      } catch {
        error.value = err?.message || 'Failed to fetch event'
        throw err
      }
    } finally {
      isLoading.value = false
    }
  }

  const getCurrentEventImage = (event: SchoolEvent) => {
    if (event.images && event.images.length > 0) {
      const index = imageIndices.value[event.id] || 0
      return event.images[index]
    }
    return event.image || ''
  }

  const getImageCount = (event: SchoolEvent) => {
    if (event.images && event.images.length > 1) {
      const current = (imageIndices.value[event.id] || 0) + 1
      return `${current}/${event.images.length}`
    }
    return null
  }

  const cycleEventImages = (list?: SchoolEvent[]) => {
    const target = list || events.value
    target.forEach((event) => {
      if (event.images && event.images.length > 1) {
        const currentIndex = imageIndices.value[event.id] || 0
        imageIndices.value[event.id] = (currentIndex + 1) % event.images.length
      }
    })
  }

  const sortEventsNewestFirst = (list: SchoolEvent[]) => {
    return [...list].sort((a, b) => {
      if (a.createdAt && b.createdAt) {
        return new Date(String(b.createdAt)).getTime() - new Date(String(a.createdAt)).getTime()
      }
      if (a.date && b.date) {
        return new Date(String(b.date)).getTime() - new Date(String(a.date)).getTime()
      }
      return Number(b.id) - Number(a.id)
    })
  }

  return {
    events,
    isLoading,
    error,
    imageIndices,
    mediaUrl,
    fetchEvents,
    fetchEventById,
    getCurrentEventImage,
    getImageCount,
    cycleEventImages,
    sortEventsNewestFirst,
  }
}

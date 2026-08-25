export const FALLBACK_RWANDA_LOCATIONS = [
  {
    code: 'kigali',
    name: 'Kigali City',
    districts: ['Gasabo', 'Kicukiro', 'Nyarugenge'],
  },
  {
    code: 'eastern',
    name: 'Eastern Province',
    districts: ['Bugesera', 'Gatsibo', 'Kayonza', 'Kirehe', 'Ngoma', 'Nyagatare', 'Rwamagana'],
  },
  {
    code: 'northern',
    name: 'Northern Province',
    districts: ['Burera', 'Gakenke', 'Gicumbi', 'Musanze', 'Rulindo'],
  },
  {
    code: 'southern',
    name: 'Southern Province',
    districts: ['Gisagara', 'Huye', 'Kamonyi', 'Muhanga', 'Nyamagabe', 'Nyanza', 'Nyaruguru', 'Ruhango'],
  },
  {
    code: 'western',
    name: 'Western Province',
    districts: ['Karongi', 'Ngororero', 'Nyabihu', 'Nyamasheke', 'Rubavu', 'Rusizi', 'Rutsiro'],
  },
]

export function formatRwandaAddress(district, province) {
  return [district, province].filter(Boolean).join(', ')
}

export function useRwandaLocations() {
  const { apiFetch } = useApi()
  const locations = ref([...FALLBACK_RWANDA_LOCATIONS])
  const loading = ref(false)

  const loadLocations = async () => {
    loading.value = true
    try {
      const res = await apiFetch('/api/locations')
      if (Array.isArray(res?.data) && res.data.length) {
        locations.value = res.data
      }
    } catch {
      locations.value = [...FALLBACK_RWANDA_LOCATIONS]
    } finally {
      loading.value = false
    }
  }

  const districtsFor = (provinceName) => {
    const match = locations.value.find((p) => p.name === provinceName || p.code === provinceName)
    return match?.districts || []
  }

  return { locations, loading, loadLocations, districtsFor }
}

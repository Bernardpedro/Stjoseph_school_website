export type SearchResult = {
  id: string
  type: string
  category: string
  title: string
  description: string
  path: string
  meta?: Record<string, unknown>
}

type ApiSearchResponse = {
  success: boolean
  message?: string
  data?: {
    query: string
    results: SearchResult[]
    total: number
  }
}

const SITE_INDEX: SearchResult[] = [
  {
    id: 'page-home',
    type: 'page',
    category: 'Pages',
    title: 'Home',
    description: 'Saint Joseph TSS Nzuki homepage — school overview, academics, campus and events',
    path: '/',
  },
  {
    id: 'page-academics',
    type: 'page',
    category: 'Pages',
    title: 'Academics',
    description: 'Combinations, short courses and requirements',
    path: '/academics',
  },
  {
    id: 'page-events',
    type: 'page',
    category: 'Pages',
    title: 'Events',
    description: 'School events, ceremonies and activities calendar',
    path: '/events',
  },
  {
    id: 'page-contacts',
    type: 'page',
    category: 'Pages',
    title: 'Contact Us',
    description: 'Phone, email, address and location of Saint Joseph TSS Nzuki',
    path: '/contacts',
  },
  {
    id: 'page-login',
    type: 'page',
    category: 'Pages',
    title: 'Login',
    description: 'Sign in to the school dashboard',
    path: '/auth/login',
  },
  {
    id: 'static-motto',
    type: 'content',
    category: 'About',
    title: 'School Motto',
    description: 'Education, Work and Research',
    path: '/',
  },
  {
    id: 'static-success',
    type: 'content',
    category: 'About',
    title: 'Nationwide Exam Success',
    description:
      'Karangwa Irakoze Roben and Aime Senga Prosper ranked among top 10 in Transport and Logistics 2025',
    path: '/academics',
  },
  {
    id: 'static-automobile',
    type: 'content',
    category: 'Academics',
    title: 'Automobile Technology',
    description: 'Vehicle repair, engine maintenance, diagnostics — school garage',
    path: '/academics?search=Automobile#academic-program',
  },
  {
    id: 'static-building',
    type: 'content',
    category: 'Academics',
    title: 'Building Construction',
    description: 'Masonry, drawing plans, construction management',
    path: '/academics?search=Building#academic-program',
  },
  {
    id: 'static-tailoring',
    type: 'content',
    category: 'Academics',
    title: 'Tailoring',
    description: 'Cutting, sewing, design, fashion — short vocational course',
    path: '/academics?search=Tailoring',
  },
  {
    id: 'static-carpentry',
    type: 'content',
    category: 'Academics',
    title: 'Carpentry',
    description: 'Furniture making, wood finishing, construction carpentry',
    path: '/academics?search=Carpentry',
  },
  {
    id: 'static-computer-lab',
    type: 'content',
    category: 'Academics',
    title: 'Computer Lab',
    description: 'Digital learning lab with computers and technology resources',
    path: '/?search=computer',
  },
  {
    id: 'static-admission',
    type: 'content',
    category: 'Admissions',
    title: 'School Admission Requirements',
    description: 'Birth certificate, school report, photographs, national ID and application form',
    path: '/admission',
  },
  {
    id: 'static-requirements',
    type: 'content',
    category: 'Requirements',
    title: 'Student Requirements',
    description: 'Uniform, protective gear, exercise books, practical tools, school fees',
    path: '/academics?search=uniform',
  },
  {
    id: 'static-classrooms',
    type: 'content',
    category: 'Campus',
    title: 'Classrooms',
    description: 'Spacious well-equipped classrooms for optimal learning',
    path: '/?search=classrooms',
  },
  {
    id: 'static-assembly',
    type: 'content',
    category: 'Campus',
    title: 'Assembly Hall',
    description: 'Hall for school gatherings, events and ceremonies',
    path: '/?search=assembly',
  },
  {
    id: 'static-dormitory',
    type: 'content',
    category: 'Campus',
    title: 'Girls Dormitory',
    description: 'Safe boarding environment for female students',
    path: '/?search=dormitory',
  },
  {
    id: 'static-boys-dorm',
    type: 'content',
    category: 'Campus',
    title: 'Boys Dormitory',
    description: 'Comfortable and secure dormitory for male students',
    path: '/?search=boys',
  },
  {
    id: 'static-partners',
    type: 'content',
    category: 'Projects',
    title: 'Partnerschaftsverein Rheinland-Pfalz',
    description: 'Projects financed by our German partnership association',
    path: '/?search=partners',
  },
  {
    id: 'static-address',
    type: 'content',
    category: 'Contact',
    title: 'School Address',
    description: 'Kirwa Village, Bihembe Cell, Kabagali Sector, Ruhango District, Southern Province, Rwanda',
    path: '/contacts?search=address',
  },
  {
    id: 'static-phone',
    type: 'content',
    category: 'Contact',
    title: 'Phone Number',
    description: '+250 783 138 446',
    path: '/contacts?search=phone',
  },
  {
    id: 'static-email',
    type: 'content',
    category: 'Contact',
    title: 'Email',
    description: 'tssnzuki@gmail.com — contact the school administration',
    path: '/contacts?search=email',
  },
]

function matchesQuery(item: SearchResult, query: string): boolean {
  const q = query.toLowerCase()
  const haystack = [item.title, item.description, item.category, item.type]
    .join(' ')
    .toLowerCase()
  return haystack.includes(q)
}

function dedupeResults(items: SearchResult[]): SearchResult[] {
  const seen = new Set<string>()
  const out: SearchResult[] = []
  for (const item of items) {
    const key = `${item.type}:${item.title.toLowerCase()}`
    if (seen.has(key)) continue
    seen.add(key)
    out.push(item)
  }
  return out
}

export function useGlobalSearch() {
  const { apiFetch } = useApi()
  const loading = ref(false)
  const results = ref<SearchResult[]>([])
  const error = ref('')
  let abortController: AbortController | null = null

  const search = async (rawQuery: string) => {
    const query = rawQuery.trim()
    error.value = ''

    if (query.length < 2) {
      results.value = []
      loading.value = false
      return []
    }

    loading.value = true
    const staticHits = SITE_INDEX.filter((item) => matchesQuery(item, query))

    let apiHits: SearchResult[] = []
    try {
      abortController?.abort()
      abortController = typeof AbortController !== 'undefined' ? new AbortController() : null

      const res = await apiFetch<ApiSearchResponse>('/api/search', {
        query: { q: query },
      })
      apiHits = res?.data?.results || []
    } catch (e: any) {
      // Still show static results if API is down
      if (e?.name !== 'AbortError') {
        error.value = 'Live search unavailable — showing site pages only'
      }
    }

    const merged = dedupeResults([...apiHits, ...staticHits]).slice(0, 25)
    results.value = merged
    loading.value = false
    return merged
  }

  const clear = () => {
    results.value = []
    error.value = ''
    loading.value = false
  }

  return { search, clear, results, loading, error, siteIndex: SITE_INDEX }
}

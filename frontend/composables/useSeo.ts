const SITE_NAME = 'Saint Joseph TSS Nzuki'
const DEFAULT_DESCRIPTION =
  'Saint Joseph Technical Secondary School Nzuki - a technical secondary school in Ruhango District, Southern Province, Rwanda.'
const DEFAULT_IMAGE = 'https://res.cloudinary.com/dck2vzccq/image/upload/v1752140933/5._efpl5u.jpg'

interface PageSeoOptions {
  title?: string
  description?: string
  image?: string
  path?: string
}

export function usePageSeo(options: PageSeoOptions = {}) {
  const config = useRuntimeConfig()
  const route = useRoute()

  const fullTitle = options.title ? `${options.title} | ${SITE_NAME}` : SITE_NAME
  const description = options.description || DEFAULT_DESCRIPTION
  const image = options.image || DEFAULT_IMAGE
  const url = `${config.public.siteUrl}${options.path ?? route.path}`

  useSeoMeta({
    title: fullTitle,
    description,
    ogTitle: fullTitle,
    ogDescription: description,
    ogImage: image,
    ogUrl: url,
    ogType: 'website',
    ogSiteName: SITE_NAME,
    twitterCard: 'summary_large_image',
    twitterTitle: fullTitle,
    twitterDescription: description,
    twitterImage: image,
  })

  useHead({
    link: [{ rel: 'canonical', href: url }],
  })
}

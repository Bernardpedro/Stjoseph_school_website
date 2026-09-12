/**
 * Inserts Cloudinary delivery transforms (auto format/quality + a max width)
 * into a Cloudinary URL. Non-Cloudinary URLs are returned unchanged, so this
 * is safe to call on any image src regardless of where it's hosted.
 */
export function cldOptimize(url: unknown, width = 800): string {
  if (typeof url !== 'string' || !url.includes('res.cloudinary.com')) {
    return (url as string) ?? ''
  }

  const marker = '/upload/'
  const idx = url.indexOf(marker)
  if (idx === -1) return url

  const insertAt = idx + marker.length
  return `${url.slice(0, insertAt)}f_auto,q_auto,w_${width}/${url.slice(insertAt)}`
}

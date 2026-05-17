const defaultSiteName = 'Sastra Arab'
const defaultDescription = 'Website resmi Program Studi Sastra Arab untuk informasi akademik, berita, kegiatan mahasiswa, dan layanan komunikasi prodi.'
const defaultImage = '/img/news/news1.jpg'

function absoluteUrl(value) {
  if (!value) return `${window.location.origin}${defaultImage}`
  if (/^https?:\/\//i.test(value)) return value
  return `${window.location.origin}/${String(value).replace(/^\/+/, '')}`
}

function setMeta(selector, attr, value) {
  let el = document.head.querySelector(selector)
  if (!el) {
    el = document.createElement('meta')
    const match = selector.match(/\[(name|property)="([^"]+)"\]/)
    if (match) el.setAttribute(match[1], match[2])
    document.head.appendChild(el)
  }
  el.setAttribute(attr, value)
}

function setCanonical(url) {
  let el = document.head.querySelector('link[rel="canonical"]')
  if (!el) {
    el = document.createElement('link')
    el.setAttribute('rel', 'canonical')
    document.head.appendChild(el)
  }
  el.setAttribute('href', url)
}

export function stripHtml(value = '') {
  return String(value)
    .replace(/<[^>]*>/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()
}

export function applySeo({
  title = defaultSiteName,
  description = defaultDescription,
  image = defaultImage,
  url = window.location.href,
  type = 'website',
  siteName = defaultSiteName,
} = {}) {
  const normalizedDescription = stripHtml(description) || defaultDescription
  const normalizedImage = absoluteUrl(image)

  document.title = title
  setCanonical(url)
  setMeta('meta[name="description"]', 'content', normalizedDescription)
  setMeta('meta[property="og:locale"]', 'content', 'id_ID')
  setMeta('meta[property="og:type"]', 'content', type)
  setMeta('meta[property="og:site_name"]', 'content', siteName)
  setMeta('meta[property="og:title"]', 'content', title)
  setMeta('meta[property="og:description"]', 'content', normalizedDescription)
  setMeta('meta[property="og:url"]', 'content', url)
  setMeta('meta[property="og:image"]', 'content', normalizedImage)
  setMeta('meta[property="og:image:secure_url"]', 'content', normalizedImage)
  setMeta('meta[name="twitter:card"]', 'content', 'summary_large_image')
  setMeta('meta[name="twitter:title"]', 'content', title)
  setMeta('meta[name="twitter:description"]', 'content', normalizedDescription)
  setMeta('meta[name="twitter:image"]', 'content', normalizedImage)
}

export function makeExcerpt(value = '', length = 160) {
  const text = stripHtml(value)
  return text.length > length ? `${text.slice(0, length - 3).trim()}...` : text
}

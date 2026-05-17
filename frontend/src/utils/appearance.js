const ACCENT_STORAGE_KEY = 'app-accent-color'
const DEFAULT_ACCENT = '#2563eb'

export function isHexColor(value) {
  return /^#[0-9a-fA-F]{6}$/.test(value || '')
}

function hexToRgb(value) {
  if (!isHexColor(value)) return null

  const hex = value.slice(1)
  return {
    r: parseInt(hex.slice(0, 2), 16),
    g: parseInt(hex.slice(2, 4), 16),
    b: parseInt(hex.slice(4, 6), 16),
  }
}

function setAccentVars(target, color) {
  if (!target || !isHexColor(color)) return

  const rgb = hexToRgb(color)
  const soft = rgb ? `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.14)` : 'rgba(37, 99, 235, 0.14)'
  const softer = rgb ? `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.08)` : 'rgba(37, 99, 235, 0.08)'

  target.style.setProperty('--color-accent', color)
  target.style.setProperty('--app-accent', color)
  target.style.setProperty('--app-accent-strong', color)
  target.style.setProperty('--app-accent-soft', soft)
  target.style.setProperty('--app-accent-softer', softer)
}

export function cacheAccentColor(color) {
  if (!isHexColor(color) || typeof localStorage === 'undefined') return
  localStorage.setItem(ACCENT_STORAGE_KEY, color)
}

export function readCachedAccentColor() {
  if (typeof localStorage === 'undefined') return ''

  const color = localStorage.getItem(ACCENT_STORAGE_KEY)
  return isHexColor(color) ? color : ''
}

export function applyAccentColor(color, options = {}) {
  const accent = isHexColor(color) ? color : DEFAULT_ACCENT

  if (options.cache !== false) {
    cacheAccentColor(accent)
  }

  setAccentVars(document.documentElement, accent)
  setAccentVars(document.body, accent)

  document
    .querySelectorAll('.admin-root, .author-root, .editor-root, .portal-root, .public-root')
    .forEach((target) => setAccentVars(target, accent))
}

export function applyCachedAccentColor() {
  const color = readCachedAccentColor()
  if (color) applyAccentColor(color, { cache: false })
}

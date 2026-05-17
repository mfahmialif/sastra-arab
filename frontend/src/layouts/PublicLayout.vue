<template>
  <div class="public-root min-h-screen overflow-hidden font-display" :data-theme="resolvedTheme">
    <Preloader :show="loading" />
    <Navbar
      :items="navItems"
      :active-section="activeSection"
      :theme="resolvedTheme"
      :brand="navbarBrand"
      :loading="navigationLoading"
      @toggle-theme="toggleTheme"
    />

    <router-view v-slot="{ Component, route: viewRoute }">
      <Transition name="public-page" mode="out-in">
        <component :is="Component" :key="viewRoute.path" />
      </Transition>
    </router-view>

    <Footer :year="year" :content="footerContent" />
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '../axios'
import Footer from '../components/layouts/public/Footer.vue'
import Navbar from '../components/layouts/public/Navbar.vue'
import Preloader from '../components/layouts/public/Preloader.vue'
import { applyAccentColor, applyCachedAccentColor } from '../utils/appearance'
import { backendAssetUrl } from '../utils/asset'
import { defaultLandingSettings, normalizeLandingSettings } from '../views/public/landing/settings'
import { usePublicTheme } from '../views/public/usePublicTheme'
import '../components/layouts/public/styles.css'

const route = useRoute()
const { resolvedTheme, initTheme, toggleTheme } = usePublicTheme()
const year = new Date().getFullYear()
const loading = ref(true)
const activeSection = ref('home')
const navigationLoading = ref(true)
const footerContent = ref(defaultLandingSettings.footer)
const navbarBrand = ref({
  mode: 'text',
  icon: 'auto_stories',
  title: 'Sastra Arab',
  subtitle: 'Program Studi',
  logoUrl: '',
})
let sectionElements = []
let ticking = false

const fallbackNavItems = [
  { label: 'Home', href: '/', id: 'home' },
  { label: 'Profil', href: '/#about', id: 'about', sectionId: 'about' },
  { label: 'Akademik', href: '/#services', id: 'services', sectionId: 'services' },
  { label: 'News', href: '/news', id: 'news' },
  { label: 'Contact', href: '/contact', id: 'contact' },
]
const navItems = ref(fallbackNavItems)

const isLanding = computed(() => route.name === 'Landing')

function updateRouteActiveSection() {
  if (route.path.startsWith('/news')) {
    activeSection.value = 'news'
    return
  }

  if (route.path.startsWith('/pages')) {
    activeSection.value = route.path
    return
  }

  if (route.path.startsWith('/contact')) {
    activeSection.value = 'contact'
    return
  }

  if (!isLanding.value) {
    activeSection.value = route.meta.publicActive || 'home'
  }
}

function collectLandingSections() {
  sectionElements = navItems.value
    .filter((item) => item.sectionId || item.id === 'home')
    .map((item) => document.getElementById(item.sectionId || item.id))
    .filter(Boolean)
}

function updateLandingActiveSection() {
  if (!isLanding.value) return

  const marker = window.scrollY + 150
  let currentSection = 'home'

  for (const section of sectionElements) {
    if (section.offsetTop <= marker) {
      currentSection = section.id
    }
  }

  activeSection.value = currentSection
}

function onScroll() {
  if (ticking) return

  window.requestAnimationFrame(() => {
    updateLandingActiveSection()
    ticking = false
  })
  ticking = true
}

function refreshPublicNavigation() {
  window.removeEventListener('scroll', onScroll)

  if (isLanding.value) {
    window.requestAnimationFrame(() => {
      collectLandingSections()
      updateLandingActiveSection()
      window.addEventListener('scroll', onScroll, { passive: true })
    })
  } else {
    updateRouteActiveSection()
  }
}

function normalizeMenuItem(item) {
  const href = item.url || item.href || '/'
  const hash = href.startsWith('/#') ? href.slice(2) : ''
  const id = hash || (href === '/' ? 'home' : href.replace(/^\/+/, '').split('/')[0] || 'home')

  return {
    label: item.label,
    href,
    target: item.target || '_self',
    id,
    sectionId: hash || undefined,
    children: (item.children || []).map(normalizeMenuItem),
  }
}

async function loadNavigation() {
  navigationLoading.value = true
  try {
    const { data } = await api.get('/navigation/primary')
    const items = (data || []).map(normalizeMenuItem)
    navItems.value = items.length ? items : fallbackNavItems
  } catch {
    navItems.value = fallbackNavItems
  } finally {
    navigationLoading.value = false
  }
}

async function loadAppearanceSettings() {
  try {
    const { data } = await api.get('/settings/general')
    if (data.accent_color) {
      applyAccentColor(data.accent_color)
    }

    const faviconUrl = backendAssetUrl(data.favicon_path || data.favicon_url)
    navbarBrand.value = {
      mode: data.navbar_brand_mode || 'text',
      icon: data.navbar_text_icon || 'auto_stories',
      title: data.navbar_text_title || data.system_name || 'Sastra Arab',
      subtitle: data.navbar_text_subtitle || 'Program Studi',
      logoUrl: backendAssetUrl(data.navbar_logo_path || data.navbar_logo_url),
    }

    if (!faviconUrl) return

    let favicon = document.querySelector("link[rel='icon']")
    if (!favicon) {
      favicon = document.createElement('link')
      favicon.rel = 'icon'
      document.head.appendChild(favicon)
    }
    favicon.href = faviconUrl
  } catch {
    // Keep the default favicon if public settings cannot be loaded.
  }
}

async function loadLandingSettings() {
  try {
    const { data } = await api.get('/settings/landing', {
      params: { _t: Date.now() },
    })
    footerContent.value = normalizeLandingSettings(data).footer
  } catch {
    footerContent.value = defaultLandingSettings.footer
  }
}

onMounted(() => {
  applyCachedAccentColor()
  initTheme()
  loadAppearanceSettings()
  loadLandingSettings()
  loadNavigation().finally(refreshPublicNavigation)

  window.setTimeout(() => {
    loading.value = false
  }, 650)
})

watch(() => route.fullPath, () => {
  refreshPublicNavigation()
})

watch(navItems, () => {
  refreshPublicNavigation()
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll)
})
</script>

<style scoped>
.public-page-enter-active {
  transition: opacity 0.32s cubic-bezier(0.4, 0, 0.2, 1), transform 0.32s cubic-bezier(0.4, 0, 0.2, 1);
}
.public-page-leave-active {
  transition: opacity 0.16s ease;
}
.public-page-enter-from {
  opacity: 0;
  transform: translateY(14px);
}
.public-page-leave-to {
  opacity: 0;
}
</style>

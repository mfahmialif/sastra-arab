<template>
  <header class="fixed inset-x-0 top-0 z-50 px-4 pt-4 sm:px-6 lg:pt-6">
    <div class="public-nav-shell mx-auto flex max-w-7xl items-center justify-between gap-4 rounded-[1.7rem] px-5 py-3 backdrop-blur-xl lg:px-7">
      <router-link to="/" class="logo-pill group" :class="{ 'logo-pill-image': showLogoImage }">
        <span v-if="showLogoImage" class="logo-image-wrap">
          <img :src="brand.logoUrl" :alt="brand.title || 'Logo'" class="logo-image" />
        </span>
        <span v-else class="logo-icon">
          <span class="material-symbols-outlined text-[22px]">{{ brand.icon || 'auto_stories' }}</span>
        </span>
        <span v-if="!showLogoImage" class="leading-tight">
          <span class="block text-base font-black text-white">{{ brand.title || 'Sastra Arab' }}</span>
          <span class="block text-[10px] font-black uppercase tracking-wide text-sky-300">{{ brand.subtitle || 'Program Studi' }}</span>
        </span>
      </router-link>

      <nav v-if="loading" class="public-nav-links hidden items-center gap-3 md:flex" aria-hidden="true">
        <span v-for="item in 5" :key="item" class="nav-skeleton-item"></span>
      </nav>

      <nav v-else class="public-nav-links hidden items-center gap-2 text-[14px] font-bold md:flex">
        <div v-for="item in items" :key="item.href" class="nav-item-wrap">
          <a
            v-if="isExternal(item.href)"
            :href="item.href"
            :target="item.target || '_self'"
            :rel="item.target === '_blank' ? 'noopener noreferrer' : null"
            class="nav-link"
            :class="{ active: isActive(item) }"
          >
            <span>{{ item.label }}</span>
            <span v-if="item.children?.length" class="nav-branch-arrow material-symbols-outlined" aria-hidden="true">keyboard_arrow_down</span>
          </a>
          <router-link v-else :to="item.href" class="nav-link" :class="{ active: isActive(item) }">
            <span>{{ item.label }}</span>
            <span v-if="item.children?.length" class="nav-branch-arrow material-symbols-outlined" aria-hidden="true">keyboard_arrow_down</span>
          </router-link>
          <div v-if="item.children?.length" class="nav-dropdown">
            <NavbarDropdown :items="item.children" />
          </div>
        </div>
      </nav>

      <div class="hidden items-center gap-2 md:flex">
        <button
          class="public-theme-toggle"
          type="button"
          :title="theme === 'dark' ? 'Aktifkan light mode' : 'Aktifkan dark mode'"
          @click="$emit('toggle-theme')"
        >
          <span class="material-symbols-outlined text-[19px]">{{ theme === 'dark' ? 'light_mode' : 'dark_mode' }}</span>
        </button>
        <router-link :to="authLink.to" class="public-signin hidden h-9 items-center gap-2 rounded-full px-3.5 text-sm font-black transition md:inline-flex">
          <span class="material-symbols-outlined text-[19px]">{{ authLink.icon }}</span>
          {{ authLink.label }}
        </router-link>
      </div>

      <button @click="toggleMobileMenu" class="public-mobile-menu-button flex size-10 items-center justify-center rounded-full md:hidden">
        <span class="material-symbols-outlined">{{ mobileOpen ? 'close' : 'menu' }}</span>
      </button>
    </div>

    <Transition name="mobile-menu">
      <div v-if="mobileOpen" class="public-mobile-panel mx-auto mt-3 max-w-7xl rounded-3xl p-4 shadow-2xl backdrop-blur-xl md:hidden">
        <template v-if="loading">
          <div v-for="item in 5" :key="item" class="mobile-skeleton-link"></div>
        </template>

        <div v-for="item in loading ? [] : items" :key="mobileItemKey(item)" class="mobile-menu-group">
          <div class="mobile-menu-row">
            <a
              v-if="isExternal(item.href)"
              :href="item.href"
              :target="item.target || '_self'"
              :rel="item.target === '_blank' ? 'noopener noreferrer' : null"
              @click="closeMobileMenu"
              class="public-mobile-link flex min-w-0 flex-1 items-center rounded-2xl px-4 py-3 font-bold"
              :class="{ active: isActive(item) }"
            >
              <span class="truncate">{{ item.label }}</span>
            </a>
            <router-link v-else :to="item.href" @click="closeMobileMenu" class="public-mobile-link flex min-w-0 flex-1 items-center rounded-2xl px-4 py-3 font-bold" :class="{ active: isActive(item) }">
              <span class="truncate">{{ item.label }}</span>
            </router-link>
            <button
              v-if="item.children?.length"
              class="mobile-submenu-toggle"
              type="button"
              :aria-expanded="isMobileExpanded(item)"
              :title="isMobileExpanded(item) ? 'Tutup submenu' : 'Buka submenu'"
              @click="toggleMobileSubmenu(item)"
            >
              <span class="mobile-branch-arrow material-symbols-outlined" :class="{ expanded: isMobileExpanded(item) }">keyboard_arrow_down</span>
            </button>
          </div>

          <div v-if="item.children?.length && isMobileExpanded(item)" class="mobile-submenu-list">
            <div
              v-for="child in flattenVisibleMenuChildren(item.children, mobileItemKey(item))"
              :key="child.key"
              class="mobile-menu-row mt-1"
              :style="{ paddingLeft: `${(child.depth + 1) * 0.65}rem` }"
            >
              <a
                v-if="isExternal(child.href)"
                :href="child.href"
                :target="child.target || '_self'"
                :rel="child.target === '_blank' ? 'noopener noreferrer' : null"
                @click="closeMobileMenu"
                class="public-mobile-link flex min-w-0 flex-1 items-center rounded-2xl px-4 py-2.5 text-sm font-bold"
              >
                <span class="truncate">{{ child.label }}</span>
              </a>
              <router-link v-else :to="child.href" @click="closeMobileMenu" class="public-mobile-link flex min-w-0 flex-1 items-center rounded-2xl px-4 py-2.5 text-sm font-bold">
                <span class="truncate">{{ child.label }}</span>
              </router-link>
              <button
                v-if="child.children?.length"
                class="mobile-submenu-toggle compact"
                type="button"
                :aria-expanded="isMobileExpanded(child)"
                :title="isMobileExpanded(child) ? 'Tutup submenu' : 'Buka submenu'"
                @click="toggleMobileSubmenu(child)"
              >
                <span class="mobile-branch-arrow material-symbols-outlined" :class="{ expanded: isMobileExpanded(child) }">keyboard_arrow_down</span>
              </button>
            </div>
          </div>
        </div>
        <button class="public-mobile-theme-toggle mt-2 flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3 font-black" type="button" @click="$emit('toggle-theme')">
          <span class="material-symbols-outlined text-[20px]">{{ theme === 'dark' ? 'light_mode' : 'dark_mode' }}</span>
          {{ theme === 'dark' ? 'Light Mode' : 'Dark Mode' }}
        </button>
        <router-link :to="authLink.to" class="public-mobile-signin mt-2 flex items-center justify-center gap-2 rounded-2xl px-4 py-3 font-black" @click="mobileOpen = false">
          <span class="material-symbols-outlined text-[20px]">{{ authLink.icon }}</span>
          {{ authLink.label }}
        </router-link>
      </div>
    </Transition>
  </header>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import NavbarDropdown from './NavbarDropdown.vue'

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
  activeSection: {
    type: String,
    default: 'home',
  },
  theme: {
    type: String,
    default: 'light',
  },
  brand: {
    type: Object,
    default: () => ({
      mode: 'text',
      icon: 'auto_stories',
      title: 'Sastra Arab',
      subtitle: 'Program Studi',
      logoUrl: '',
    }),
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

defineEmits(['toggle-theme'])

const mobileOpen = ref(false)
const mobileExpanded = ref({})
const authStore = useAuthStore()
const showLogoImage = computed(() => props.brand?.mode === 'logo' && Boolean(props.brand?.logoUrl))

const dashboardRoute = computed(() => {
  const roleName = authStore.user?.role?.name

  if (roleName === 'Penulis') {
    return { name: 'PenulisDashboard' }
  }

  if (roleName === 'Kepala Penulis') {
    return { name: 'KepalaPenulisDashboard' }
  }

  return { name: 'AdminDashboard' }
})

const authLink = computed(() => {
  if (authStore.isAuthenticated) {
    return {
      to: dashboardRoute.value,
      icon: 'dashboard',
      label: 'Dashboard',
    }
  }

  return {
    to: '/login',
    icon: 'login',
    label: 'Sign In',
  }
})

function isExternal(href = '') {
  return /^https?:\/\//i.test(href) || href.startsWith('mailto:') || href.startsWith('tel:')
}

function isActive(item) {
  return props.activeSection === item.id || props.activeSection === item.href
}

function mobileItemKey(item, parentKey = '') {
  return [parentKey, item.id, item.href, item.label].filter(Boolean).join('::')
}

function isMobileExpanded(item) {
  return Boolean(mobileExpanded.value[item.key || mobileItemKey(item)])
}

function toggleMobileSubmenu(item) {
  const key = item.key || mobileItemKey(item)
  mobileExpanded.value = {
    ...mobileExpanded.value,
    [key]: !mobileExpanded.value[key],
  }
}

function flattenVisibleMenuChildren(items = [], parentKey = '', depth = 0) {
  return items.flatMap((item) => {
    const key = mobileItemKey(item, parentKey)
    const row = { ...item, key, depth }

    if (!item.children?.length || !mobileExpanded.value[key]) {
      return [row]
    }

    return [
      row,
      ...flattenVisibleMenuChildren(item.children, key, depth + 1),
    ]
  })
}

function closeMobileMenu() {
  mobileOpen.value = false
}

function toggleMobileMenu() {
  mobileOpen.value = !mobileOpen.value
}

onMounted(() => {
  if (!authStore.hasCheckedSession) {
    authStore.fetchUser()
  }
})

watch(mobileOpen, (isOpen) => {
  document.body.style.overflow = isOpen ? 'hidden' : ''

  if (!isOpen) {
    mobileExpanded.value = {}
  }
})

onBeforeUnmount(() => {
  document.body.style.overflow = ''
})
</script>

<style scoped>
.nav-skeleton-item,
.mobile-skeleton-link {
  position: relative;
  overflow: hidden;
  border-radius: 999px;
  background: linear-gradient(135deg, rgba(226, 232, 240, 0.64), rgba(248, 250, 252, 0.82), rgba(226, 232, 240, 0.58));
}

.nav-skeleton-item::after,
.mobile-skeleton-link::after {
  content: '';
  position: absolute;
  inset: 0;
  transform: translateX(-100%);
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.66), transparent);
  animation: navSkeletonSweep 1.35s ease-in-out infinite;
}

.nav-skeleton-item {
  width: 4.7rem;
  height: 1.05rem;
}

.nav-skeleton-item:nth-child(2) {
  width: 5.4rem;
}

.nav-skeleton-item:nth-child(3) {
  width: 6rem;
}

.mobile-skeleton-link {
  height: 2.95rem;
  margin-bottom: 0.5rem;
  border-radius: 1rem;
}

:global(.public-root[data-theme='dark']) .nav-skeleton-item,
:global(.public-root[data-theme='dark']) .mobile-skeleton-link {
  background: linear-gradient(135deg, rgba(15, 23, 42, 0.82), rgba(30, 41, 59, 0.7), rgba(15, 23, 42, 0.86));
}

:global(.public-root[data-theme='dark']) .nav-skeleton-item::after,
:global(.public-root[data-theme='dark']) .mobile-skeleton-link::after {
  background: linear-gradient(90deg, transparent, var(--public-accent-soft), transparent);
}

@keyframes navSkeletonSweep {
  to {
    transform: translateX(100%);
  }
}

.public-mobile-panel {
  max-height: calc(100dvh - 6.5rem);
  overflow-y: auto;
  overscroll-behavior: contain;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
}

.mobile-menu-row {
  display: flex;
  align-items: center;
  gap: 0.45rem;
}

.mobile-submenu-toggle {
  display: inline-flex;
  width: 2.75rem;
  height: 2.75rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border: 1px solid var(--public-border);
  border-radius: 1rem;
  background: var(--public-accent-soft);
  color: var(--public-accent-strong);
  transition: background 0.2s ease, transform 0.2s ease;
}

.mobile-submenu-toggle.compact {
  width: 2.45rem;
  height: 2.45rem;
  border-radius: 0.85rem;
}

.mobile-submenu-toggle:hover {
  background: var(--public-accent);
  color: white;
}

.mobile-branch-arrow {
  transition: transform 0.2s ease;
}

.mobile-branch-arrow.expanded {
  transform: rotate(180deg);
}

.mobile-submenu-list {
  margin: 0.35rem 0 0.55rem 0.65rem;
  border-left: 1px solid var(--public-border);
  padding-left: 0.15rem;
}
</style>

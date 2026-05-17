<template>
  <Teleport to="body">
    <Transition name="search-overlay">
      <div v-if="open" class="search-backdrop" :data-theme="theme" @click.self="close">
        <div class="search-panel" role="dialog" aria-modal="true" aria-label="Pencarian dashboard">
          <div class="search-top">
            <div class="search-icon">
              <span class="material-symbols-outlined">search</span>
            </div>
            <input
              ref="searchInputRef"
              v-model="query"
              class="search-field"
              type="text"
              placeholder="Cari menu, halaman, atau aksi..."
              autocomplete="off"
              @keydown.esc.prevent="close"
              @keydown.enter.prevent="openFirstResult"
            />
            <button class="close-btn" type="button" title="Tutup" @click="close">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <div class="search-body">
            <div class="search-meta">
              <span>{{ filteredItems.length }} hasil</span>
              <span class="hidden sm:inline">Tekan Enter untuk membuka hasil pertama</span>
            </div>

            <div v-if="filteredItems.length" class="result-list">
              <button
                v-for="item in filteredItems"
                :key="item.to"
                class="result-item"
                type="button"
                @click="goTo(item.to)"
              >
                <span class="result-icon material-symbols-outlined">{{ item.icon }}</span>
                <span class="min-w-0">
                  <span class="result-title">{{ item.title }}</span>
                  <span class="result-desc">{{ item.description }}</span>
                </span>
                <span class="result-arrow material-symbols-outlined">arrow_forward</span>
              </button>
            </div>

            <div v-else class="empty-state">
              <span class="material-symbols-outlined">manage_search</span>
              <p>Tidak ada hasil untuk pencarian ini.</p>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue'
import { useRouter } from 'vue-router'

const props = defineProps({
  open: { type: Boolean, default: false },
  portal: { type: String, default: 'admin' },
  theme: { type: String, default: 'dark' },
})

const emit = defineEmits(['close'])
const router = useRouter()
const query = ref('')
const searchInputRef = ref(null)

const searchItems = {
  admin: [
    { title: 'Dashboard', description: 'Ringkasan aktivitas admin', icon: 'dashboard', to: '/administrator/dashboard' },
    { title: 'News', description: 'Kelola berita dan artikel', icon: 'article', to: '/administrator/news' },
    { title: 'Tambah News', description: 'Buat artikel atau berita baru', icon: 'add_circle', to: '/administrator/news/create' },
    { title: 'Kategori News', description: 'Kelola kategori berita', icon: 'category', to: '/administrator/news-categories' },
    { title: 'Manajemen User', description: 'Kelola akun pengguna', icon: 'group', to: '/administrator/manajemen-user' },
    { title: 'Manajemen Role', description: 'Kelola role dan akses', icon: 'admin_panel_settings', to: '/administrator/manajemen-role' },
    { title: 'Pengaturan', description: 'Konfigurasi sistem, email, Google Login', icon: 'settings', to: '/administrator/pengaturan' },
    { title: 'Profile', description: 'Profile akun admin', icon: 'person', to: '/administrator/profile' },
  ],
  penulis: [
    { title: 'Dashboard', description: 'Ringkasan penulis', icon: 'dashboard', to: '/penulis/dashboard' },
    { title: 'Artikel Saya', description: 'Kelola artikel milik sendiri', icon: 'article', to: '/penulis/news' },
    { title: 'Tambah Artikel', description: 'Tulis artikel baru', icon: 'add_circle', to: '/penulis/news/create' },
    { title: 'Profile Penulis', description: 'Profil akun penulis', icon: 'person', to: '/penulis/profile' },
  ],
  kepalaPenulis: [
    { title: 'Dashboard', description: 'Ringkasan kepala penulis', icon: 'dashboard', to: '/kepala-penulis/dashboard' },
    { title: 'Artikel Penulis', description: 'Kelola artikel semua penulis', icon: 'article', to: '/kepala-penulis/news' },
    { title: 'Tambah Artikel Penulis', description: 'Buat artikel untuk penulis', icon: 'add_circle', to: '/kepala-penulis/news/create' },
    { title: 'Akun Penulis', description: 'Kelola akun penulis', icon: 'group', to: '/kepala-penulis/writers' },
    { title: 'Profile Kepala Penulis', description: 'Profil akun kepala penulis', icon: 'person', to: '/kepala-penulis/profile' },
  ],
}

const filteredItems = computed(() => {
  const items = searchItems[props.portal] || searchItems.admin
  const keyword = query.value.trim().toLowerCase()
  if (!keyword) return items

  return items.filter((item) => `${item.title} ${item.description}`.toLowerCase().includes(keyword))
})

function close() {
  emit('close')
}

function goTo(to) {
  router.push(to)
  close()
}

function openFirstResult() {
  if (filteredItems.value[0]) {
    goTo(filteredItems.value[0].to)
  }
}

watch(() => props.open, async (value) => {
  if (value) {
    query.value = ''
    await nextTick()
    searchInputRef.value?.focus()
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onBeforeUnmount(() => {
  document.body.style.overflow = ''
})
</script>

<style scoped>
.search-backdrop {
  --search-bg-card: #141d38;
  --search-bg-input: #111a33;
  --search-bg-hover: rgba(37, 99, 235, 0.08);
  --search-border: #1c2850;
  --search-border-light: #243362;
  --search-text-heading: #ffffff;
  --search-text-body: #cbd5e1;
  --search-text-muted: #64748b;
  --search-shadow: 0 34px 120px rgba(0, 0, 0, 0.46);
  --search-backdrop-bg: rgba(2, 6, 23, 0.62);
  position: fixed;
  inset: 0;
  z-index: 9999;
  display: grid;
  place-items: center;
  padding: 1rem;
  background: var(--search-backdrop-bg);
  backdrop-filter: blur(18px) saturate(0.9);
  -webkit-backdrop-filter: blur(18px) saturate(0.9);
}
.search-backdrop[data-theme="light"] {
  --search-bg-card: #ffffff;
  --search-bg-input: #f8fafc;
  --search-bg-hover: rgba(37, 99, 235, 0.1);
  --search-border: #dce3ec;
  --search-border-light: #cbd5e1;
  --search-text-heading: #0f172a;
  --search-text-body: #475569;
  --search-text-muted: #64748b;
  --search-shadow: 0 34px 110px rgba(15, 23, 42, 0.24);
  --search-backdrop-bg: rgba(226, 232, 240, 0.72);
}
.search-backdrop[data-theme="dark"] {
  color-scheme: dark;
}
.search-backdrop[data-theme="light"] {
  color-scheme: light;
}
.search-panel {
  position: relative;
  width: min(760px, 100%);
  max-height: min(76vh, 680px);
  overflow: hidden;
  border: 1px solid var(--search-border);
  border-radius: 1.35rem;
  background: var(--search-bg-card);
  box-shadow: var(--search-shadow);
}
.search-top {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 0.85rem;
  border-bottom: 1px solid var(--search-border);
  padding: 1rem;
}
.search-icon,
.close-btn,
.result-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.search-icon,
.close-btn {
  width: 2.6rem;
  height: 2.6rem;
  border-radius: 0.9rem;
  background: var(--search-bg-input);
  color: var(--color-accent, #2563eb);
}
.close-btn {
  color: var(--search-text-muted);
  transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
}
.close-btn:hover {
  background: rgba(239, 68, 68, 0.12);
  color: #ef4444;
  transform: rotate(8deg);
}
.search-field {
  min-width: 0;
  background: transparent;
  color: var(--search-text-heading);
  font-size: clamp(1.15rem, 2.2vw, 1.7rem);
  font-weight: 900;
  outline: none;
}
.search-field::placeholder {
  color: var(--search-text-muted);
}
.search-body {
  max-height: calc(min(76vh, 680px) - 4.75rem);
  overflow: auto;
  padding: 1rem;
}
.search-meta {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 0.85rem;
  color: var(--search-text-muted);
  font-size: 0.78rem;
  font-weight: 850;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}
.result-list {
  display: grid;
  gap: 0.55rem;
}
.result-item {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr) auto;
  align-items: center;
  gap: 0.85rem;
  width: 100%;
  border: 1px solid transparent;
  border-radius: 1rem;
  padding: 0.85rem;
  color: var(--search-text-body);
  text-align: left;
  transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}
.result-item:hover {
  border-color: color-mix(in srgb, var(--color-accent, #2563eb) 42%, transparent);
  background: var(--search-bg-hover);
  transform: translateY(-1px);
}
.result-icon {
  width: 2.65rem;
  height: 2.65rem;
  border-radius: 0.85rem;
  background: var(--search-bg-input);
  color: var(--color-accent, #2563eb);
}
.result-title,
.result-desc {
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.result-title {
  color: var(--search-text-heading);
  font-size: 0.96rem;
  font-weight: 950;
}
.result-desc {
  margin-top: 0.15rem;
  color: var(--search-text-muted);
  font-size: 0.82rem;
  font-weight: 650;
}
.result-arrow {
  color: var(--search-text-muted);
  font-size: 1.15rem;
}
.empty-state {
  display: grid;
  justify-items: center;
  gap: 0.65rem;
  padding: 3rem 1rem;
  color: var(--search-text-muted);
  text-align: center;
  font-weight: 800;
}
.empty-state .material-symbols-outlined {
  color: var(--color-accent, #2563eb);
  font-size: 2.4rem;
}
.search-overlay-enter-active,
.search-overlay-leave-active {
  transition: opacity 0.22s ease;
}
.search-overlay-enter-active .search-panel,
.search-overlay-leave-active .search-panel {
  transition: transform 0.22s ease, opacity 0.22s ease;
}
.search-overlay-enter-from,
.search-overlay-leave-to {
  opacity: 0;
}
.search-overlay-enter-from .search-panel,
.search-overlay-leave-to .search-panel {
  opacity: 0;
  transform: translateY(14px) scale(0.98);
}

@media (max-width: 640px) {
  .search-backdrop {
    align-items: start;
    padding-top: 5rem;
  }
  .search-panel {
    max-height: calc(100vh - 6rem);
  }
  .search-body {
    max-height: calc(100vh - 11rem);
  }
  .search-top {
    gap: 0.65rem;
    padding: 0.8rem;
  }
  .search-icon,
  .close-btn {
    width: 2.35rem;
    height: 2.35rem;
  }
  .search-field {
    font-size: 1rem;
  }
}
</style>

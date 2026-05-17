<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-sm font-black uppercase tracking-[0.18em] text-accent">Website Pages</p>
        <h1 class="mt-1 text-2xl font-black tracking-tight sm:text-3xl" style="color: var(--text-heading)">Manajemen Page</h1>
        <p class="mt-1 max-w-2xl text-sm" style="color: var(--text-muted)">Kelola halaman statis yang dipakai oleh menu dan tampilan publik website.</p>
      </div>
      <router-link :to="{ name: 'AdminPagesCreate' }" class="add-btn inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-black shadow-[0_0_15px_rgba(37,99,235,0.3)] sm:w-auto" style="color: var(--text-btn)">
        <span class="material-symbols-outlined text-[20px]">add_circle</span>
        Tambah Page
      </router-link>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div v-for="stat in stats" :key="stat.label" class="stat-card rounded-xl border border-accent/20 p-5">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-accent">{{ stat.label }}</p>
            <p class="mt-3 text-3xl font-black leading-none" style="color: var(--text-heading)">{{ stat.value }}</p>
            <p class="mt-2 text-sm" style="color: var(--text-muted)">{{ stat.caption }}</p>
          </div>
          <span class="stat-icon material-symbols-outlined">{{ stat.icon }}</span>
        </div>
      </div>
    </div>

    <div class="filter-card rounded-2xl p-4">
      <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_220px_auto] md:items-center">
        <label class="relative min-w-0">
          <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-accent text-[20px]">search</span>
          <input v-model="search" class="input input-with-icon w-full" placeholder="Cari judul, slug, atau pembuat..." @input="debouncedFetch" />
        </label>
        <select v-model="status" class="input w-full" @change="fetchData">
          <option value="all">Semua Status</option>
          <option value="Published">Published</option>
          <option value="Draft">Draft</option>
        </select>
        <button class="refresh-btn inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl px-4 text-sm font-black md:w-auto" type="button" @click="fetchData">
          <span class="material-symbols-outlined text-[19px]">refresh</span>
          Refresh
        </button>
      </div>
    </div>

    <div class="table-card rounded-2xl p-2">
      <div class="flex flex-col gap-1 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-base font-black" style="color: var(--text-heading)">Daftar Page</h2>
        <p class="text-sm font-semibold" style="color: var(--text-muted)">Menampilkan {{ items.length }} page</p>
      </div>
      <div v-if="loading" class="flex justify-center py-12">
        <span class="material-symbols-outlined animate-spin text-4xl text-accent">progress_activity</span>
      </div>
      <div v-else class="overflow-x-auto">
        <table class="min-w-[760px] w-full text-left">
          <thead>
            <tr style="background: var(--bg-input)">
              <th class="px-4 py-4 text-sm font-bold" style="color: var(--text-heading)">Judul</th>
              <th class="px-4 py-4 text-sm font-bold" style="color: var(--text-heading)">Slug</th>
              <th class="px-4 py-4 text-sm font-bold" style="color: var(--text-heading)">Status</th>
              <th class="px-4 py-4 text-right text-sm font-bold" style="color: var(--text-heading)">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="items.length === 0">
              <td colspan="4" class="px-4 py-12 text-center text-sm" style="color: var(--text-muted)">Belum ada page</td>
            </tr>
            <tr v-for="item in items" :key="item.id" class="table-row">
              <td class="px-4 py-4">
                <p class="font-black" style="color: var(--text-heading)">{{ item.title }}</p>
                <p class="text-sm" style="color: var(--text-muted)">{{ item.creator?.name || 'Admin' }}</p>
              </td>
              <td class="px-4 py-4 text-sm" style="color: var(--text-muted)">
                <span class="inline-block max-w-[16rem] truncate align-bottom">/pages/{{ item.slug }}</span>
              </td>
              <td class="px-4 py-4"><span :class="item.status === 'Published' ? 'app-badge app-badge--success' : 'app-badge app-badge--blue'">{{ item.status }}</span></td>
              <td class="px-4 py-4 text-right">
                <router-link :to="{ name: 'AdminPagesEdit', params: { id: item.id } }" class="rounded-lg p-2 text-accent hover:bg-white/5" title="Edit">
                  <span class="material-symbols-outlined text-[20px]">edit</span>
                </router-link>
                <button class="rounded-lg p-2 text-red-400 hover:bg-white/5" title="Hapus" @click="remove(item)">
                  <span class="material-symbols-outlined text-[20px]">delete</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import api from '../../../axios'

const items = ref([])
const loading = ref(false)
const search = ref('')
const status = ref('all')
let timer = null

const stats = computed(() => {
  const total = items.value.length
  const published = items.value.filter((item) => item.status === 'Published').length
  const draft = items.value.filter((item) => item.status === 'Draft').length
  const visible = status.value === 'all' ? total : items.value.filter((item) => item.status === status.value).length

  return [
    { label: 'Total Page', value: total, icon: 'description', caption: 'Data page termuat' },
    { label: 'Published', value: published, icon: 'task_alt', caption: 'Tampil di publik' },
    { label: 'Draft', value: draft, icon: 'draft', caption: 'Masih disimpan' },
    { label: 'Hasil Filter', value: visible, icon: 'filter_alt', caption: status.value === 'all' ? 'Semua status' : status.value },
  ]
})

function debouncedFetch() {
  clearTimeout(timer)
  timer = setTimeout(fetchData, 300)
}

async function fetchData() {
  loading.value = true
  try {
    const { data } = await api.get('/pages', { params: { search: search.value, status: status.value, per_page: 100 } })
    items.value = data.data || data || []
  } finally {
    loading.value = false
  }
}

async function remove(item) {
  if (!confirm(`Hapus page "${item.title}"?`)) return
  await api.delete(`/pages/${item.id}`)
  await fetchData()
}

onMounted(fetchData)
</script>

<style scoped>
.filter-card,
.table-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
}

.add-btn,
.refresh-btn {
  transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.add-btn:hover,
.refresh-btn:hover {
  transform: translateY(-1px);
}

.refresh-btn {
  background: var(--bg-input);
  border: 1px solid var(--border);
  color: var(--text-heading);
}

.refresh-btn:hover {
  border-color: var(--color-accent);
  color: var(--color-accent);
}

.stat-card {
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at top right, color-mix(in srgb, var(--color-accent) 16%, transparent), transparent 16rem);
  pointer-events: none;
}

.stat-icon {
  display: inline-flex;
  width: 2.75rem;
  height: 2.75rem;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  border-radius: 0.85rem;
  background: color-mix(in srgb, var(--color-accent) 12%, transparent);
  color: var(--color-accent);
  font-size: 1.45rem;
}

.input { border-radius: 0.85rem; background: var(--bg-input); border: 1px solid var(--border); color: var(--text-heading); padding: 0.7rem 0.9rem; outline: none; min-height: 2.75rem; }
.input-with-icon { padding-left: 2.75rem; }
.input:focus { border-color: var(--color-accent); box-shadow: 0 0 12px rgba(37, 99, 235, 0.18); }
.table-row { border-top: 1px solid var(--border); transition: background 0.2s ease; }
.table-row:hover { background: var(--hover-nav-bg); }

@media (max-width: 640px) {
  .table-card {
    margin-inline: -0.25rem;
  }
}
</style>

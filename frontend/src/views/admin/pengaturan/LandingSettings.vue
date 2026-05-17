<template>
  <div class="settings-card rounded-xl overflow-hidden">
    <div class="card-header px-6 py-4 flex items-center justify-between gap-4">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-accent text-[20px]">dashboard_customize</span>
        <h3 class="font-bold" style="color: var(--text-heading)">Pengaturan Landing Page</h3>
      </div>
      <button class="save-btn flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold cursor-pointer transition-all disabled:opacity-60" :disabled="saving" @click="saveLandingSettings">
        <span class="material-symbols-outlined text-[16px]">save</span>
        Simpan Landing
      </button>
    </div>

    <div class="px-6 py-5">
      <div class="landing-tabs">
        <button v-for="tab in tabs" :key="tab.key" type="button" :class="{ active: activeTab === tab.key }" @click="activeTab = tab.key">
          {{ tab.label }}
        </button>
      </div>

      <div class="mt-6 space-y-5">
        <template v-if="activeTab === 'hero'">
          <div class="setting-row">
            <div class="setting-label">
              <h4>Foto Hero</h4>
              <p>Foto utama halaman depan. Upload di sini agar bisa diganti tiap periode.</p>
            </div>
            <div class="setting-control">
              <div class="flex flex-col gap-4">
                <div class="h-56 w-full overflow-hidden rounded-xl border border-dashed border-accent/40" style="background: var(--bg-input)">
                  <img v-if="heroImagePreview" :src="heroImagePreview" alt="Preview foto hero" class="h-full w-full object-contain" />
                  <div v-else class="flex h-full items-center justify-center">
                    <span class="material-symbols-outlined text-accent text-4xl">image</span>
                  </div>
                </div>
                <label class="upload-btn flex w-fit items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold cursor-pointer transition-all">
                  <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                  Upload Foto Hero
                  <input class="sr-only" type="file" accept="image/png,image/jpeg,image/webp" @change="handleHeroImageChange" />
                </label>
              </div>
            </div>
          </div>
          <TextField label="Alt Foto Hero" description="Deskripsi foto untuk aksesibilitas." v-model="form.hero.image_alt" />
          <TextField label="Eyebrow Hero" v-model="form.hero.eyebrow" />
          <TextField label="Judul Utama Hero" v-model="form.hero.main_title" />
          <TextAreaField label="Deskripsi Hero" v-model="form.hero.description" />
          <TextField label="Label Tombol Utama" v-model="form.hero.primary_label" />
          <TextField label="Link Tombol Utama" v-model="form.hero.primary_href" />
          <TextField label="Label Tombol Kedua" v-model="form.hero.secondary_label" />
          <TextField label="Link Tombol Kedua" v-model="form.hero.secondary_href" />
          <TextField label="Label Hero" description="Teks kecil pada foto hero." v-model="form.hero.label" />
          <TextAreaField label="Judul Hero" description="Nama atau judul besar pada foto hero." v-model="form.hero.title" />
        </template>

        <template v-else-if="activeTab === 'about'">
          <SectionTextFields :section="form.about" body />
          <StatsCardsEditor
            :items="form.about.stats"
            @add="addItem(form.about.stats, templates.stat)"
            @remove="removeItem(form.about.stats, $event)"
          />
        </template>

        <template v-else-if="activeTab === 'visionMission'">
          <VisionMissionSettingsEditor
            :section="form.visionMission"
            @add="addItem(form.visionMission.items, templates.mission)"
            @remove="removeItem(form.visionMission.items, $event)"
          />
        </template>

        <template v-else-if="activeTab === 'services'">
          <SectionTextFields :section="form.services" />
          <ServiceCardsEditor :items="form.services.items" @add="addItem(form.services.items, templates.service)" @remove="removeItem(form.services.items, $event)" />
        </template>

        <template v-else-if="activeTab === 'supporters'">
          <SectionTextFields :section="form.supporters" />
          <SupporterCardsEditor
            :items="form.supporters.items"
            :logo-previews="supporterLogoPreviews"
            @add="addItem(form.supporters.items, templates.supporter)"
            @remove="removeSupporterItem"
            @logo-change="handleSupporterLogoChange"
          />
        </template>

        <template v-else-if="activeTab === 'publishCta'">
          <SectionTextFields :section="form.publishCta" />
          <TextField label="Label Tombol" v-model="form.publishCta.button_label" />
          <TextField label="Link Tombol" v-model="form.publishCta.button_href" />
        </template>

        <template v-else-if="activeTab === 'contact'">
          <SectionTextFields :section="form.contact" body />
          <TextField label="Email" v-model="form.contact.email" />
          <TextField label="Telepon" v-model="form.contact.phone" />
          <TextField label="Alamat" v-model="form.contact.address" />
          <TextField label="Label Login" v-model="form.contact.login_label" />
        </template>

        <template v-else-if="activeTab === 'footer'">
          <FooterSettingsEditor
            :footer="form.footer"
            :template="templates.footerLink"
            @add-navigation="addItem(form.footer.navigation, templates.footerLink)"
            @remove-navigation="removeItem(form.footer.navigation, $event)"
            @add-service="addItem(form.footer.services, templates.footerLink)"
            @remove-service="removeItem(form.footer.services, $event)"
          />
        </template>

        <template v-else-if="activeTab === 'news'">
          <NumberField
            label="Jumlah News Ditampilkan"
            description="Jumlah berita terbaru yang muncul di section News landing page."
            :min="1"
            :max="12"
            v-model="form.news.item_count"
          />
        </template>

        <template v-else-if="activeTab === 'strengths'">
          <SectionTextFields :section="form.strengths" />
          <StrengthCardsEditor
            :items="form.strengths.items"
            @add="addItem(form.strengths.items, templates.strength)"
            @remove="removeItem(form.strengths.items, $event)"
          />
        </template>

        <template v-else-if="activeTab === 'flow'">
          <SectionTextFields :section="form.flow" />
          <FlowCardsEditor
            :items="form.flow.items"
            @add="addItem(form.flow.items, templates.flow)"
            @remove="removeItem(form.flow.items, $event)"
          />
        </template>

        <template v-else-if="activeTab === 'testimonials'">
          <SectionTextFields :section="form.testimonials" />
          <TestimonialCardsEditor
            :items="form.testimonials.items"
            @add="addItem(form.testimonials.items, templates.testimonial)"
            @remove="removeItem(form.testimonials.items, $event)"
          />
        </template>

        <template v-else>
          <SectionTextFields :section="currentSection" />
          <ArrayEditor
            :title="currentTab?.itemTitle || 'Item'"
            :items="currentSection.items"
            :template="currentTab?.template"
            :fields="currentTab?.fields"
            @add="addItem(currentSection.items, currentTab?.template)"
            @remove="removeItem(currentSection.items, $event)"
          />
        </template>

        <Transition name="fade">
          <div v-if="message.text" class="message-box" :class="message.type">
            <span class="material-symbols-outlined text-[18px]">{{ message.type === 'success' ? 'check_circle' : 'error' }}</span>
            <span>{{ message.text }}</span>
          </div>
        </Transition>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, defineComponent, h, nextTick, onMounted, reactive, ref } from 'vue'
import VueMultiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import api from '../../../axios'
import { backendAssetUrl } from '../../../utils/asset'
import { defaultLandingSettings, normalizeLandingSettings } from '../../public/landing/settings'

const tabs = [
  { key: 'hero', label: 'Hero' },
  { key: 'visionMission', label: 'Visi Misi' },
  { key: 'services', label: 'Akademik', itemTitle: 'Layanan', template: { icon: 'school', title: '', body: '', href: '#' }, fields: ['icon', 'title', 'body', 'href'] },
  { key: 'supporters', label: 'Supporter', itemTitle: 'Supporter', template: { display_type: 'icon', icon: 'school', logo_path: '', logo_url: '', name: '' }, fields: ['icon', 'name'] },
  { key: 'news', label: 'News' },
  { key: 'about', label: 'Tentang' },
  { key: 'publishCta', label: 'CTA' },
  { key: 'strengths', label: 'Keunggulan', itemTitle: 'Keunggulan', template: { icon: 'star', title: '', body: '' }, fields: ['icon', 'title', 'body'] },
  { key: 'flow', label: 'Kegiatan', itemTitle: 'Alur', template: { no: '01', title: '', body: '' }, fields: ['no', 'title', 'body'] },
  { key: 'testimonials', label: 'Testimoni', itemTitle: 'Testimoni', template: { quote: '', name: '', role: '' }, fields: ['quote', 'name', 'role'] },
  { key: 'contact', label: 'Kontak' },
  { key: 'footer', label: 'Footer' },
]
const templates = {
  service: { icon: 'school', title: '', body: '', href: '#' },
  supporter: { display_type: 'icon', icon: 'school', logo_path: '', logo_url: '', name: '' },
  stat: { value: '', label: '' },
  mission: { title: '', body: '' },
  strength: { icon: 'star', title: '', body: '' },
  flow: { no: '01', title: '', body: '' },
  testimonial: { quote: '', name: '', role: '' },
  footerLink: { label: '', href: '/' },
}
const fields = {
  stat: ['value', 'label'],
  footerLink: ['label', 'href'],
}

const fieldLabels = {
  body: 'Deskripsi',
  category: 'Kategori',
  day: 'Tanggal',
  href: 'Link',
  icon: 'Icon',
  image: 'Gambar',
  label: 'Label',
  month: 'Bulan',
  name: 'Nama',
  no: 'Nomor',
  quote: 'Kutipan',
  role: 'Peran',
  title: 'Judul',
  value: 'Nilai',
}

const materialIconOptions = [
  'school',
  'translate',
  'groups',
  'support_agent',
  'menu_book',
  'history_edu',
  'forum',
  'public',
  'auto_stories',
  'record_voice_over',
  'diversity_3',
  'science',
  'handshake',
  'star',
  'workspace_premium',
  'campaign',
  'event',
  'contact_support',
]

const activeTab = ref('hero')
const saving = ref(false)
const heroImageFile = ref(null)
const heroImagePreview = ref('')
const supporterLogoFiles = ref({})
const supporterLogoPreviews = ref({})
const message = reactive({ type: '', text: '' })
const form = reactive(normalizeLandingSettings(defaultLandingSettings))

const currentTab = computed(() => tabs.find((tab) => tab.key === activeTab.value))
const currentSection = computed(() => form[activeTab.value] || {})
const SAVE_TIMEOUT_MS = 20000
const MIN_LOADER_MS = 350

function wait(ms) {
  return new Promise((resolve) => window.setTimeout(resolve, ms))
}

async function finishLoader(startedAt) {
  const elapsed = Date.now() - startedAt
  if (elapsed < MIN_LOADER_MS) {
    await wait(MIN_LOADER_MS - elapsed)
  }
}

function replaceForm(data) {
  Object.assign(form, normalizeLandingSettings(data))
  heroImagePreview.value = backendAssetUrl(form.hero.image_path || form.hero.image_url)
}

function handleHeroImageChange(event) {
  const file = event.target.files?.[0]
  if (!file) return
  heroImageFile.value = file
  heroImagePreview.value = URL.createObjectURL(file)
}

function addItem(items, template) {
  items.push(JSON.parse(JSON.stringify(template || {})))
}

function removeItem(items, index) {
  if (items.length <= 1) return
  items.splice(index, 1)
}

function removeSupporterItem(index) {
  removeItem(form.supporters.items, index)
  supporterLogoFiles.value = reindexByRemovedIndex(supporterLogoFiles.value, index)
  supporterLogoPreviews.value = reindexByRemovedIndex(supporterLogoPreviews.value, index)
}

function reindexByRemovedIndex(items, removedIndex) {
  return Object.entries(items).reduce((result, [key, value]) => {
    const index = Number(key)
    if (index < removedIndex) {
      result[index] = value
    } else if (index > removedIndex) {
      result[index - 1] = value
    }
    return result
  }, {})
}

function handleSupporterLogoChange(index, event) {
  const file = event.target.files?.[0]
  if (!file) return

  supporterLogoFiles.value = {
    ...supporterLogoFiles.value,
    [index]: file,
  }
  supporterLogoPreviews.value = {
    ...supporterLogoPreviews.value,
    [index]: URL.createObjectURL(file),
  }

  if (form.supporters.items[index]) {
    form.supporters.items[index].display_type = 'logo'
  }
}

async function loadLandingSettings() {
  try {
    const { data } = await api.get('/settings/landing')
    replaceForm(data)
  } catch {
    message.type = 'error'
    message.text = 'Gagal memuat pengaturan landing page.'
  }
}

async function saveLandingSettings() {
  if (saving.value) return false

  const startedAt = Date.now()
  saving.value = true
  message.type = ''
  message.text = ''

  const formData = new FormData()
  formData.append('content', JSON.stringify(form))
  if (heroImageFile.value) {
    formData.append('hero_image', heroImageFile.value)
  }
  Object.entries(supporterLogoFiles.value).forEach(([index, file]) => {
    if (file) {
      formData.append(`supporter_logos[${index}]`, file)
    }
  })

  try {
    const { data } = await api.post('/settings/landing', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
      timeout: SAVE_TIMEOUT_MS,
    })
    heroImageFile.value = null
    supporterLogoFiles.value = {}
    supporterLogoPreviews.value = {}
    replaceForm(data)
    message.type = 'success'
    message.text = 'Pengaturan landing page berhasil disimpan.'
    return true
  } catch (error) {
    const errors = error.response?.data?.errors || {}
    message.type = 'error'
    message.text = error.code === 'ECONNABORTED'
      ? 'Menyimpan terlalu lama. Coba ulangi atau cek koneksi backend.'
      : Object.values(errors)?.[0]?.[0] || 'Gagal menyimpan pengaturan landing page.'
    return false
  } finally {
    await finishLoader(startedAt)
    saving.value = false
  }
}

defineExpose({
  saveLandingSettings,
})

const TextField = defineComponent({
  props: ['modelValue', 'label', 'description'],
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    return () => h('div', { class: 'setting-row' }, [
      h('div', { class: 'setting-label' }, [
        h('h4', props.label),
        props.description ? h('p', props.description) : null,
      ]),
      h('div', { class: 'setting-control' }, [
        h('input', {
          class: 'setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
          value: props.modelValue,
          onInput: (event) => emit('update:modelValue', event.target.value),
        }),
      ]),
    ])
  },
})

const NumberField = defineComponent({
  props: ['modelValue', 'label', 'description', 'min', 'max'],
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    function updateValue(event) {
      const value = Number(event.target.value)
      emit('update:modelValue', Number.isFinite(value) ? value : props.min || 1)
    }

    return () => h('div', { class: 'setting-row' }, [
      h('div', { class: 'setting-label' }, [
        h('h4', props.label),
        props.description ? h('p', props.description) : null,
      ]),
      h('div', { class: 'setting-control' }, [
        h('input', {
          class: 'setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
          type: 'number',
          min: props.min,
          max: props.max,
          value: props.modelValue,
          onInput: updateValue,
        }),
      ]),
    ])
  },
})

const TextAreaField = defineComponent({
  props: ['modelValue', 'label', 'description'],
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    return () => h('div', { class: 'setting-row' }, [
      h('div', { class: 'setting-label' }, [
        h('h4', props.label),
        props.description ? h('p', props.description) : null,
      ]),
      h('div', { class: 'setting-control' }, [
        h('textarea', {
          class: 'setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
          rows: 3,
          value: props.modelValue,
          onInput: (event) => emit('update:modelValue', event.target.value),
        }),
      ]),
    ])
  },
})

const SectionTextFields = defineComponent({
  props: ['section', 'body', 'bodyKey'],
  setup(props) {
    const key = props.bodyKey || 'body'
    return () => h('div', { class: 'space-y-5' }, [
      h(TextField, { label: 'Kicker', modelValue: props.section.kicker, 'onUpdate:modelValue': (value) => { props.section.kicker = value } }),
      h(TextAreaField, { label: 'Judul Section', modelValue: props.section.title, 'onUpdate:modelValue': (value) => { props.section.title = value } }),
      props.body ? h(TextAreaField, { label: 'Deskripsi', modelValue: props.section[key], 'onUpdate:modelValue': (value) => { props.section[key] = value } }) : null,
    ])
  },
})

const IconMultiselect = defineComponent({
  props: ['modelValue'],
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const multiselectRef = ref(null)

    function closeDropdown() {
      const multiselect = multiselectRef.value
      if (!multiselect) return

      multiselect.deactivate?.()
      multiselect.isOpen = false
      if ('search' in multiselect) {
        multiselect.search = ''
      }
      multiselect.$refs?.search?.blur?.()
      multiselect.$el?.blur?.()
    }

    function closeDropdownAfterSelect() {
      closeDropdown()
      nextTick(() => {
        closeDropdown()
      })
      window.setTimeout(closeDropdown, 0)
    }

    function updateValue(value) {
      emit('update:modelValue', value || '')
      closeDropdownAfterSelect()
    }

    function addTag(value) {
      if (!materialIconOptions.includes(value)) {
        materialIconOptions.push(value)
      }
      updateValue(value)
    }

    return () => h(VueMultiselect, {
      ref: multiselectRef,
      modelValue: props.modelValue || '',
      'onUpdate:modelValue': updateValue,
      options: materialIconOptions,
      searchable: true,
      taggable: true,
      closeOnSelect: true,
      'close-on-select': true,
      allowEmpty: false,
      showLabels: false,
      placeholder: 'Pilih icon',
      class: 'service-icon-select',
      onSelect: closeDropdownAfterSelect,
      onTag: addTag,
    }, {
      singleLabel: ({ option }) => h('span', { class: 'icon-select-option' }, [
        h('span', { class: 'material-symbols-outlined' }, option),
        h('span', option),
      ]),
      option: ({ option }) => h('span', { class: 'icon-select-option' }, [
        h('span', { class: 'material-symbols-outlined' }, option),
        h('span', option),
      ]),
    })
  },
})

const ServiceCardsEditor = defineComponent({
  props: ['items'],
  emits: ['add', 'remove'],
  setup(props, { emit }) {
    const serviceFields = [
      { key: 'icon', label: 'Icon Material', description: 'Contoh: school, translate, groups, support_agent.' },
      { key: 'title', label: 'Judul Card' },
      { key: 'body', label: 'Deskripsi Card', textarea: true },
      { key: 'href', label: 'Link Card', description: 'Contoh: #services, /news, atau https://...' },
    ]

    return () => h('div', { class: 'array-editor service-editor' }, [
      h('div', { class: 'array-editor-head' }, [
        h('div', { class: 'service-editor-title' }, [
          h('div', [
            h('h4', 'Card Akademik'),
            h('p', { class: 'array-help' }, 'Atur card yang tampil di section Akademik. Tambah, hapus, dan ubah isi langsung dari sini.'),
          ]),
          h('span', { class: 'service-count' }, `${(props.items || []).length} card`),
        ]),
        h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, [
          h('span', { class: 'material-symbols-outlined' }, 'add'),
          'Tambah Card',
        ]),
      ]),
      h('div', { class: 'service-card-list' }, [
        ...(props.items || []).map((item, index) => h('div', { class: 'service-card-editor', key: index }, [
          h('div', { class: 'service-card-head' }, [
            h('div', { class: 'service-card-title' }, [
              h('span', { class: 'service-card-number' }, String(index + 1).padStart(2, '0')),
              h('span', { class: 'service-card-icon material-symbols-outlined' }, item.icon || 'school'),
              h('div', [
                h('strong', item.title || `Card Akademik ${index + 1}`),
                h('small', item.body || 'Deskripsi card akan tampil di halaman landing.'),
              ]),
            ]),
            h('button', { class: 'array-remove icon-remove', type: 'button', title: 'Hapus card', onClick: () => emit('remove', index) }, [
              h('span', { class: 'material-symbols-outlined' }, 'delete'),
            ]),
          ]),
          h('div', { class: 'service-field-grid' }, serviceFields.map((field) => h('label', { class: field.textarea ? 'service-field full' : 'service-field' }, [
            h('span', field.label),
            field.description ? h('small', field.description) : null,
            field.key === 'icon'
              ? h(IconMultiselect, {
                modelValue: item.icon || '',
                'onUpdate:modelValue': (value) => { item.icon = value || '' },
              })
              : h(field.textarea ? 'textarea' : 'input', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                rows: field.textarea ? 3 : undefined,
                value: item[field.key],
                onInput: (event) => { item[field.key] = event.target.value },
              }),
          ]))),
        ])),
        !(props.items || []).length ? h('div', { class: 'service-empty' }, [
          h('span', { class: 'material-symbols-outlined' }, 'view_carousel'),
          h('strong', 'Belum ada card akademik'),
          h('p', { class: 'array-help' }, 'Atur isi card yang tampil di section Akademik. Jumlah card bisa ditambah sesuai kebutuhan.'),
          h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, 'Tambah Card Pertama'),
        ]) : null,
      ]),
    ])
  },
})

const SupporterCardsEditor = defineComponent({
  props: ['items', 'logoPreviews'],
  emits: ['add', 'remove', 'logo-change'],
  setup(props, { emit }) {
    return () => h('div', { class: 'array-editor service-editor' }, [
      h('div', { class: 'array-editor-head' }, [
        h('div', { class: 'service-editor-title' }, [
          h('div', [
            h('h4', 'Card Supporter'),
            h('p', { class: 'array-help' }, 'Atur daftar supporter yang tampil di section Supporter.'),
          ]),
          h('span', { class: 'service-count' }, `${(props.items || []).length} card`),
        ]),
        h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, [
          h('span', { class: 'material-symbols-outlined' }, 'add'),
          'Tambah Card',
        ]),
      ]),
      h('div', { class: 'service-card-list supporter-card-list' }, [
        ...(props.items || []).map((item, index) => {
          const logoSrc = props.logoPreviews?.[index] || backendAssetUrl(item.logo_path || item.logo_url)
          const isLogo = item.display_type === 'logo'

          return h('div', { class: 'service-card-editor supporter-card-editor', key: index }, [
            h('div', { class: 'service-card-head' }, [
              h('div', { class: 'service-card-title' }, [
                h('span', { class: 'service-card-number' }, String(index + 1).padStart(2, '0')),
                item.display_type === 'text'
                  ? null
                  : isLogo && logoSrc
                  ? h('span', { class: 'supporter-logo-preview sm' }, [
                    h('img', { src: logoSrc, alt: item.name || `Supporter ${index + 1}` }),
                  ])
                  : h('span', { class: 'service-card-icon material-symbols-outlined' }, item.icon || 'school'),
                h('div', [
                  h('strong', item.name || `Supporter ${index + 1}`),
                  h('small', item.display_type === 'text' ? 'Teks saja' : isLogo && logoSrc ? 'Logo supporter' : item.icon || 'school'),
                ]),
              ]),
              h('button', { class: 'array-remove icon-remove', type: 'button', title: 'Hapus card', onClick: () => emit('remove', index) }, [
                h('span', { class: 'material-symbols-outlined' }, 'delete'),
              ]),
            ]),
            h('div', { class: 'supporter-mode-toggle' }, [
              h('button', {
                type: 'button',
                class: item.display_type !== 'logo' && item.display_type !== 'text' ? 'active' : '',
                onClick: () => { item.display_type = 'icon' },
              }, [
                h('span', { class: 'material-symbols-outlined' }, 'interests'),
                'Icon',
              ]),
              h('button', {
                type: 'button',
                class: item.display_type === 'logo' ? 'active' : '',
                onClick: () => { item.display_type = 'logo' },
              }, [
                h('span', { class: 'material-symbols-outlined' }, 'image'),
                'Logo',
              ]),
              h('button', {
                type: 'button',
                class: item.display_type === 'text' ? 'active' : '',
                onClick: () => { item.display_type = 'text' },
              }, [
                h('span', { class: 'material-symbols-outlined' }, 'title'),
                'Teks',
              ]),
            ]),
            h('div', { class: 'service-field-grid' }, [
              item.display_type === 'logo' ? h('label', { class: 'service-field' }, [
                  h('span', 'Logo Supporter'),
                  h('small', 'Upload PNG, JPG, WebP, atau SVG. Logo akan tampil di section Supporter.'),
                  h('div', { class: 'supporter-logo-upload' }, [
                    h('div', { class: 'supporter-logo-preview' }, [
                      logoSrc
                        ? h('img', { src: logoSrc, alt: item.name || 'Preview logo supporter' })
                        : h('span', { class: 'material-symbols-outlined' }, 'image'),
                    ]),
                    h('label', { class: 'upload-btn supporter-upload-btn' }, [
                      h('span', { class: 'material-symbols-outlined' }, 'cloud_upload'),
                      'Upload Logo',
                      h('input', {
                        class: 'sr-only',
                        type: 'file',
                        accept: 'image/png,image/jpeg,image/webp,image/svg+xml',
                        onChange: (event) => emit('logo-change', index, event),
                      }),
                    ]),
                  ]),
                ])
                : item.display_type === 'text' ? null : h('label', { class: 'service-field' }, [
                  h('span', 'Icon Material'),
                  h('small', 'Pilih icon Material Symbols atau ketik icon custom.'),
                  h(IconMultiselect, {
                    modelValue: item.icon || '',
                    'onUpdate:modelValue': (value) => { item.icon = value || '' },
                  }),
                ]),
              h('label', { class: 'service-field' }, [
                h('span', 'Nama Supporter'),
                h('input', {
                  class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                  value: item.name,
                  onInput: (event) => { item.name = event.target.value },
                }),
              ]),
            ]),
          ])
        }),
        !(props.items || []).length ? h('div', { class: 'service-empty' }, [
          h('span', { class: 'material-symbols-outlined' }, 'diversity_3'),
          h('strong', 'Belum ada card supporter'),
          h('p', { class: 'array-help' }, 'Tambahkan supporter yang ingin ditampilkan di landing page.'),
          h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, 'Tambah Card Pertama'),
        ]) : null,
      ]),
    ])
  },
})

const StatsCardsEditor = defineComponent({
  props: ['items'],
  emits: ['add', 'remove'],
  setup(props, { emit }) {
    return () => h('div', { class: 'array-editor service-editor stats-editor' }, [
      h('div', { class: 'array-editor-head' }, [
        h('div', { class: 'service-editor-title' }, [
          h('div', [
            h('h4', 'Statistik Tentang'),
            h('p', { class: 'array-help' }, 'Atur angka ringkas yang tampil di section Tentang.'),
          ]),
          h('span', { class: 'service-count' }, `${(props.items || []).length} statistik`),
        ]),
        h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, [
          h('span', { class: 'material-symbols-outlined' }, 'add'),
          'Tambah Statistik',
        ]),
      ]),
      h('div', { class: 'stats-card-list' }, [
        ...(props.items || []).map((item, index) => h('div', { class: 'service-card-editor stats-card-editor', key: index }, [
          h('div', { class: 'stats-card-preview' }, [
            h('span', { class: 'stats-card-number' }, item.value || '0'),
            h('small', item.label || `Statistik ${index + 1}`),
          ]),
          h('div', { class: 'service-field-grid stats-field-grid' }, [
            h('label', { class: 'service-field' }, [
              h('span', 'Nilai'),
              h('input', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                value: item.value,
                placeholder: 'Contoh: 30+',
                onInput: (event) => { item.value = event.target.value },
              }),
            ]),
            h('label', { class: 'service-field' }, [
              h('span', 'Label'),
              h('input', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                value: item.label,
                placeholder: 'Contoh: Kegiatan Akademik',
                onInput: (event) => { item.label = event.target.value },
              }),
            ]),
          ]),
          h('button', { class: 'array-remove stats-remove', type: 'button', title: 'Hapus statistik', onClick: () => emit('remove', index) }, [
            h('span', { class: 'material-symbols-outlined' }, 'delete'),
            'Hapus',
          ]),
        ])),
        !(props.items || []).length ? h('div', { class: 'service-empty' }, [
          h('span', { class: 'material-symbols-outlined' }, 'monitoring'),
          h('strong', 'Belum ada statistik'),
          h('p', { class: 'array-help' }, 'Tambahkan angka ringkas untuk memperkuat section Tentang.'),
          h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, 'Tambah Statistik Pertama'),
        ]) : null,
      ]),
    ])
  },
})

const VisionMissionSettingsEditor = defineComponent({
  props: ['section'],
  emits: ['add', 'remove'],
  setup(props, { emit }) {
    return () => h('div', { class: 'vision-admin-grid' }, [
      h('section', { class: 'footer-panel vision-admin-panel' }, [
        h('div', { class: 'footer-panel-head' }, [
          h('span', { class: 'material-symbols-outlined' }, 'visibility'),
          h('div', [
            h('h4', 'Visi'),
            h('p', 'Teks visi utama yang tampil setelah hero.'),
          ]),
        ]),
        h(TextField, { label: 'Kicker', modelValue: props.section.kicker, 'onUpdate:modelValue': (value) => { props.section.kicker = value } }),
        h(TextAreaField, { label: 'Judul Section', modelValue: props.section.title, 'onUpdate:modelValue': (value) => { props.section.title = value } }),
        h(TextField, { label: 'Label Visi', modelValue: props.section.vision_title, 'onUpdate:modelValue': (value) => { props.section.vision_title = value } }),
        h(TextAreaField, { label: 'Isi Visi', modelValue: props.section.vision_body, 'onUpdate:modelValue': (value) => { props.section.vision_body = value } }),
        h(TextField, { label: 'Label Misi', modelValue: props.section.mission_title, 'onUpdate:modelValue': (value) => { props.section.mission_title = value } }),
      ]),
      h('section', { class: 'array-editor service-editor mission-admin-panel' }, [
        h('div', { class: 'array-editor-head' }, [
          h('div', { class: 'service-editor-title' }, [
            h('div', [
              h('h4', 'Daftar Misi'),
              h('p', { class: 'array-help' }, 'Atur card misi yang tampil di sisi kanan section Visi Misi.'),
            ]),
            h('span', { class: 'service-count' }, `${(props.section.items || []).length} misi`),
          ]),
          h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, [
            h('span', { class: 'material-symbols-outlined' }, 'add'),
            'Tambah Misi',
          ]),
        ]),
        h('div', { class: 'mission-admin-list' }, [
          ...(props.section.items || []).map((item, index) => h('div', { class: 'service-card-editor mission-admin-card', key: index }, [
            h('div', { class: 'service-card-head' }, [
              h('div', { class: 'service-card-title' }, [
                h('span', { class: 'service-card-icon mission-number-icon' }, String(index + 1).padStart(2, '0')),
                h('div', [
                  h('strong', item.title || `Misi ${index + 1}`),
                  h('small', item.body || 'Deskripsi misi akan tampil di landing page.'),
                ]),
              ]),
              h('button', { class: 'array-remove icon-remove', type: 'button', title: 'Hapus misi', onClick: () => emit('remove', index) }, [
                h('span', { class: 'material-symbols-outlined' }, 'delete'),
              ]),
            ]),
            h('div', { class: 'service-field-grid' }, [
              h('label', { class: 'service-field' }, [
                h('span', 'Judul Misi'),
                h('input', {
                  class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                  value: item.title,
                  onInput: (event) => { item.title = event.target.value },
                }),
              ]),
              h('label', { class: 'service-field full' }, [
                h('span', 'Deskripsi'),
                h('textarea', {
                  class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                  rows: 3,
                  value: item.body,
                  onInput: (event) => { item.body = event.target.value },
                }),
              ]),
            ]),
          ])),
        ]),
      ]),
    ])
  },
})

const StrengthCardsEditor = defineComponent({
  props: ['items'],
  emits: ['add', 'remove'],
  setup(props, { emit }) {
    return () => h('div', { class: 'array-editor service-editor strength-editor' }, [
      h('div', { class: 'array-editor-head' }, [
        h('div', { class: 'service-editor-title' }, [
          h('div', [
            h('h4', 'Card Keunggulan'),
            h('p', { class: 'array-help' }, 'Atur poin keunggulan yang tampil di section Kenapa Sastra Arab.'),
          ]),
          h('span', { class: 'service-count' }, `${(props.items || []).length} card`),
        ]),
        h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, [
          h('span', { class: 'material-symbols-outlined' }, 'add'),
          'Tambah Keunggulan',
        ]),
      ]),
      h('div', { class: 'strength-card-list' }, [
        ...(props.items || []).map((item, index) => h('div', { class: 'service-card-editor strength-card-editor', key: index }, [
          h('div', { class: 'service-card-head' }, [
            h('div', { class: 'service-card-title' }, [
              h('span', { class: 'service-card-number' }, String(index + 1).padStart(2, '0')),
              h('span', { class: 'service-card-icon material-symbols-outlined' }, item.icon || 'star'),
              h('div', [
                h('strong', item.title || `Keunggulan ${index + 1}`),
                h('small', item.body || 'Deskripsi keunggulan akan tampil di landing page.'),
              ]),
            ]),
            h('button', { class: 'array-remove icon-remove', type: 'button', title: 'Hapus keunggulan', onClick: () => emit('remove', index) }, [
              h('span', { class: 'material-symbols-outlined' }, 'delete'),
            ]),
          ]),
          h('div', { class: 'service-field-grid' }, [
            h('label', { class: 'service-field' }, [
              h('span', 'Icon Material'),
              h('small', 'Pilih icon Material Symbols atau ketik icon custom.'),
              h(IconMultiselect, {
                modelValue: item.icon || '',
                'onUpdate:modelValue': (value) => { item.icon = value || '' },
              }),
            ]),
            h('label', { class: 'service-field' }, [
              h('span', 'Judul Keunggulan'),
              h('input', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                value: item.title,
                placeholder: 'Contoh: Kompetensi Bahasa',
                onInput: (event) => { item.title = event.target.value },
              }),
            ]),
            h('label', { class: 'service-field full' }, [
              h('span', 'Deskripsi'),
              h('textarea', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                rows: 3,
                value: item.body,
                placeholder: 'Tulis deskripsi singkat keunggulan.',
                onInput: (event) => { item.body = event.target.value },
              }),
            ]),
          ]),
        ])),
        !(props.items || []).length ? h('div', { class: 'service-empty' }, [
          h('span', { class: 'material-symbols-outlined' }, 'workspace_premium'),
          h('strong', 'Belum ada card keunggulan'),
          h('p', { class: 'array-help' }, 'Tambahkan poin keunggulan yang ingin ditampilkan di landing page.'),
          h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, 'Tambah Keunggulan Pertama'),
        ]) : null,
      ]),
    ])
  },
})

const FlowCardsEditor = defineComponent({
  props: ['items'],
  emits: ['add', 'remove'],
  setup(props, { emit }) {
    return () => h('div', { class: 'array-editor service-editor flow-editor' }, [
      h('div', { class: 'array-editor-head' }, [
        h('div', { class: 'service-editor-title' }, [
          h('div', [
            h('h4', 'Card Kegiatan'),
            h('p', { class: 'array-help' }, 'Atur urutan kegiatan atau alur pengalaman belajar yang tampil di landing page.'),
          ]),
          h('span', { class: 'service-count' }, `${(props.items || []).length} kegiatan`),
        ]),
        h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, [
          h('span', { class: 'material-symbols-outlined' }, 'add'),
          'Tambah Kegiatan',
        ]),
      ]),
      h('div', { class: 'flow-card-list' }, [
        ...(props.items || []).map((item, index) => h('div', { class: 'service-card-editor flow-card-editor', key: index }, [
          h('div', { class: 'service-card-head' }, [
            h('div', { class: 'service-card-title' }, [
              h('span', { class: 'flow-step-preview' }, item.no || String(index + 1).padStart(2, '0')),
              h('div', [
                h('strong', item.title || `Kegiatan ${index + 1}`),
                h('small', item.body || 'Deskripsi kegiatan akan tampil di landing page.'),
              ]),
            ]),
            h('button', { class: 'array-remove icon-remove', type: 'button', title: 'Hapus kegiatan', onClick: () => emit('remove', index) }, [
              h('span', { class: 'material-symbols-outlined' }, 'delete'),
            ]),
          ]),
          h('div', { class: 'service-field-grid flow-field-grid' }, [
            h('label', { class: 'service-field' }, [
              h('span', 'Nomor'),
              h('input', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                value: item.no,
                placeholder: '01',
                onInput: (event) => { item.no = event.target.value },
              }),
            ]),
            h('label', { class: 'service-field' }, [
              h('span', 'Judul Kegiatan'),
              h('input', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                value: item.title,
                placeholder: 'Contoh: Pembelajaran',
                onInput: (event) => { item.title = event.target.value },
              }),
            ]),
            h('label', { class: 'service-field full' }, [
              h('span', 'Deskripsi'),
              h('textarea', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                rows: 3,
                value: item.body,
                placeholder: 'Tulis deskripsi singkat kegiatan.',
                onInput: (event) => { item.body = event.target.value },
              }),
            ]),
          ]),
        ])),
        !(props.items || []).length ? h('div', { class: 'service-empty' }, [
          h('span', { class: 'material-symbols-outlined' }, 'event_note'),
          h('strong', 'Belum ada kegiatan'),
          h('p', { class: 'array-help' }, 'Tambahkan kegiatan yang ingin ditampilkan di landing page.'),
          h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, 'Tambah Kegiatan Pertama'),
        ]) : null,
      ]),
    ])
  },
})

const TestimonialCardsEditor = defineComponent({
  props: ['items'],
  emits: ['add', 'remove'],
  setup(props, { emit }) {
    return () => h('div', { class: 'array-editor service-editor testimonial-editor' }, [
      h('div', { class: 'array-editor-head' }, [
        h('div', { class: 'service-editor-title' }, [
          h('div', [
            h('h4', 'Card Testimoni'),
            h('p', { class: 'array-help' }, 'Atur kutipan, nama, dan peran yang tampil di section Testimoni.'),
          ]),
          h('span', { class: 'service-count' }, `${(props.items || []).length} testimoni`),
        ]),
        h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, [
          h('span', { class: 'material-symbols-outlined' }, 'add'),
          'Tambah Testimoni',
        ]),
      ]),
      h('div', { class: 'testimonial-card-list' }, [
        ...(props.items || []).map((item, index) => h('div', { class: 'service-card-editor testimonial-card-editor', key: index }, [
          h('div', { class: 'testimonial-preview' }, [
            h('span', { class: 'material-symbols-outlined' }, 'format_quote'),
            h('p', item.quote || 'Kutipan testimoni akan tampil di sini.'),
            h('strong', item.name || `Nama ${index + 1}`),
            h('small', item.role || 'Peran'),
          ]),
          h('div', { class: 'service-field-grid' }, [
            h('label', { class: 'service-field full' }, [
              h('span', 'Kutipan'),
              h('textarea', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                rows: 3,
                value: item.quote,
                placeholder: 'Tulis kutipan testimoni.',
                onInput: (event) => { item.quote = event.target.value },
              }),
            ]),
            h('label', { class: 'service-field' }, [
              h('span', 'Nama'),
              h('input', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                value: item.name,
                placeholder: 'Nama pemberi testimoni',
                onInput: (event) => { item.name = event.target.value },
              }),
            ]),
            h('label', { class: 'service-field' }, [
              h('span', 'Peran'),
              h('input', {
                class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
                value: item.role,
                placeholder: 'Contoh: Mahasiswa',
                onInput: (event) => { item.role = event.target.value },
              }),
            ]),
          ]),
          h('button', { class: 'array-remove stats-remove', type: 'button', title: 'Hapus testimoni', onClick: () => emit('remove', index) }, [
            h('span', { class: 'material-symbols-outlined' }, 'delete'),
            'Hapus',
          ]),
        ])),
        !(props.items || []).length ? h('div', { class: 'service-empty' }, [
          h('span', { class: 'material-symbols-outlined' }, 'reviews'),
          h('strong', 'Belum ada testimoni'),
          h('p', { class: 'array-help' }, 'Tambahkan testimoni yang ingin ditampilkan di landing page.'),
          h('button', { class: 'service-add-btn', type: 'button', onClick: () => emit('add') }, 'Tambah Testimoni Pertama'),
        ]) : null,
      ]),
    ])
  },
})

const FooterLinkEditor = defineComponent({
  props: ['title', 'items', 'template'],
  emits: ['add', 'remove'],
  setup(props, { emit }) {
    return () => h('div', { class: 'footer-link-editor' }, [
      h('div', { class: 'footer-link-head' }, [
        h('h5', props.title),
        h('button', { type: 'button', onClick: () => emit('add') }, [
          h('span', { class: 'material-symbols-outlined' }, 'add'),
          'Tambah',
        ]),
      ]),
      h('div', { class: 'footer-link-list' }, [
        ...(props.items || []).map((item, index) => h('div', { class: 'footer-link-item', key: index }, [
          h('span', { class: 'footer-link-index' }, String(index + 1).padStart(2, '0')),
          h('label', { class: 'service-field' }, [
            h('span', 'Label'),
            h('input', {
              class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
              value: item.label,
              onInput: (event) => { item.label = event.target.value },
            }),
          ]),
          h('label', { class: 'service-field' }, [
            h('span', 'Link'),
            h('input', {
              class: 'setting-input service-input w-full rounded-lg px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
              value: item.href,
              onInput: (event) => { item.href = event.target.value },
            }),
          ]),
          h('button', { class: 'array-remove icon-remove footer-link-remove', type: 'button', title: 'Hapus link', onClick: () => emit('remove', index) }, [
            h('span', { class: 'material-symbols-outlined' }, 'delete'),
          ]),
        ])),
      ]),
    ])
  },
})

const FooterSettingsEditor = defineComponent({
  props: ['footer', 'template'],
  emits: ['add-navigation', 'remove-navigation', 'add-service', 'remove-service'],
  setup(props, { emit }) {
    return () => h('div', { class: 'footer-settings-grid' }, [
      h('section', { class: 'footer-panel footer-panel-wide' }, [
        h('div', { class: 'footer-panel-head' }, [
          h('span', { class: 'material-symbols-outlined' }, 'storefront'),
          h('div', [
            h('h4', 'Brand & CTA'),
            h('p', 'Identitas utama dan tombol aksi footer.'),
          ]),
        ]),
        h('div', { class: 'footer-field-grid' }, [
          h(TextField, { label: 'Judul Brand', modelValue: props.footer.brand_title, 'onUpdate:modelValue': (value) => { props.footer.brand_title = value } }),
          h(TextField, { label: 'Subjudul Brand', modelValue: props.footer.brand_subtitle, 'onUpdate:modelValue': (value) => { props.footer.brand_subtitle = value } }),
          h(TextAreaField, { label: 'Deskripsi Footer', modelValue: props.footer.description, 'onUpdate:modelValue': (value) => { props.footer.description = value } }),
          h(TextField, { label: 'Label Tombol Utama', modelValue: props.footer.primary_label, 'onUpdate:modelValue': (value) => { props.footer.primary_label = value } }),
          h(TextField, { label: 'Link Tombol Utama', modelValue: props.footer.primary_href, 'onUpdate:modelValue': (value) => { props.footer.primary_href = value } }),
          h(TextField, { label: 'Label Tombol Kedua', modelValue: props.footer.secondary_label, 'onUpdate:modelValue': (value) => { props.footer.secondary_label = value } }),
          h(TextField, { label: 'Link Tombol Kedua', modelValue: props.footer.secondary_href, 'onUpdate:modelValue': (value) => { props.footer.secondary_href = value } }),
        ]),
      ]),
      h('section', { class: 'footer-panel' }, [
        h('div', { class: 'footer-panel-head' }, [
          h('span', { class: 'material-symbols-outlined' }, 'near_me'),
          h('div', [
            h('h4', 'Navigasi'),
            h('p', 'Link utama yang tampil di kolom navigasi footer.'),
          ]),
        ]),
        h(TextField, { label: 'Judul Kolom', modelValue: props.footer.navigation_title, 'onUpdate:modelValue': (value) => { props.footer.navigation_title = value } }),
        h(FooterLinkEditor, {
          title: 'Link Navigasi',
          items: props.footer.navigation,
          template: props.template,
          onAdd: () => emit('add-navigation'),
          onRemove: (index) => emit('remove-navigation', index),
        }),
      ]),
      h('section', { class: 'footer-panel' }, [
        h('div', { class: 'footer-panel-head' }, [
          h('span', { class: 'material-symbols-outlined' }, 'design_services'),
          h('div', [
            h('h4', 'Layanan'),
            h('p', 'Link layanan yang tampil di footer.'),
          ]),
        ]),
        h(TextField, { label: 'Judul Kolom', modelValue: props.footer.services_title, 'onUpdate:modelValue': (value) => { props.footer.services_title = value } }),
        h(FooterLinkEditor, {
          title: 'Link Layanan',
          items: props.footer.services,
          template: props.template,
          onAdd: () => emit('add-service'),
          onRemove: (index) => emit('remove-service', index),
        }),
      ]),
      h('section', { class: 'footer-panel footer-panel-wide' }, [
        h('div', { class: 'footer-panel-head' }, [
          h('span', { class: 'material-symbols-outlined' }, 'contact_mail'),
          h('div', [
            h('h4', 'Kontak & Legal'),
            h('p', 'Informasi kontak, maps, login admin, dan copyright.'),
          ]),
        ]),
        h('div', { class: 'footer-field-grid compact' }, [
          h(TextField, { label: 'Judul Kontak', modelValue: props.footer.contact_title, 'onUpdate:modelValue': (value) => { props.footer.contact_title = value } }),
          h(TextField, { label: 'Email Footer', modelValue: props.footer.email, 'onUpdate:modelValue': (value) => { props.footer.email = value } }),
          h(TextField, { label: 'Telepon Footer', modelValue: props.footer.phone, 'onUpdate:modelValue': (value) => { props.footer.phone = value } }),
          h(TextField, { label: 'Nomor WhatsApp', description: 'Gunakan format angka, contoh 6281200000000.', modelValue: props.footer.whatsapp, 'onUpdate:modelValue': (value) => { props.footer.whatsapp = value } }),
          h(TextField, { label: 'Alamat Footer', modelValue: props.footer.address, 'onUpdate:modelValue': (value) => { props.footer.address = value } }),
          h(TextField, { label: 'Link Maps', modelValue: props.footer.maps_href, 'onUpdate:modelValue': (value) => { props.footer.maps_href = value } }),
          h(TextField, { label: 'Label Maps', modelValue: props.footer.maps_label, 'onUpdate:modelValue': (value) => { props.footer.maps_label = value } }),
          h(TextField, { label: 'Label Admin', modelValue: props.footer.admin_label, 'onUpdate:modelValue': (value) => { props.footer.admin_label = value } }),
          h(TextField, { label: 'Link Admin', modelValue: props.footer.admin_href, 'onUpdate:modelValue': (value) => { props.footer.admin_href = value } }),
          h(TextField, { label: 'Teks Copyright', modelValue: props.footer.copyright, 'onUpdate:modelValue': (value) => { props.footer.copyright = value } }),
        ]),
      ]),
    ])
  },
})

const ArrayEditor = defineComponent({
  props: ['title', 'items', 'template', 'fields'],
  emits: ['add', 'remove'],
  setup(props, { emit }) {
    return () => h('div', { class: 'array-editor' }, [
      h('div', { class: 'array-editor-head' }, [
        h('h4', props.title),
        h('button', { type: 'button', onClick: () => emit('add') }, 'Tambah'),
      ]),
      ...(props.items || []).map((item, index) => h('div', { class: 'array-item', key: index }, [
        h('button', { class: 'array-remove', type: 'button', onClick: () => emit('remove', index) }, 'Hapus'),
        ...(props.fields || []).map((field) => h('label', { class: 'array-field' }, [
          h('span', fieldLabels[field] || field),
          h(field === 'body' || field === 'quote' ? 'textarea' : 'input', {
            class: 'setting-input w-full rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-accent',
            rows: 2,
            value: item[field],
            onInput: (event) => { item[field] = event.target.value },
          }),
        ])),
      ])),
    ])
  },
})

onMounted(loadLandingSettings)
</script>

<style scoped>
.settings-card { background: var(--bg-card); border: 1px solid var(--border); }
.card-header { border-bottom: 1px solid var(--border); }
.landing-tabs {
  display: flex;
  gap: 0.55rem;
  overflow-x: auto;
  padding-bottom: 0.35rem;
}
.landing-tabs button {
  flex: 0 0 auto;
  cursor: pointer;
  border: 1px solid var(--border);
  border-radius: 999px;
  background: var(--bg-input);
  color: var(--text-muted);
  padding: 0.55rem 0.9rem;
  font-size: 0.8rem;
  font-weight: 900;
  transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}
.landing-tabs button:hover {
  border-color: color-mix(in srgb, var(--color-accent) 55%, var(--border));
  background: color-mix(in srgb, var(--color-accent) 10%, var(--bg-input));
  color: var(--text-heading);
  box-shadow: 0 10px 22px color-mix(in srgb, var(--color-accent) 14%, transparent);
  transform: translateY(-2px);
}
.landing-tabs button:active {
  transform: translateY(0) scale(0.98);
}
.landing-tabs button.active {
  border-color: var(--color-accent);
  background: color-mix(in srgb, var(--color-accent) 16%, var(--bg-input));
  color: var(--text-heading);
  box-shadow: 0 10px 24px color-mix(in srgb, var(--color-accent) 18%, transparent);
}
.setting-row {
  display: grid;
  grid-template-columns: minmax(0, 0.85fr) minmax(0, 1.35fr);
  gap: 1.25rem;
  align-items: start;
}
.setting-control {
  min-width: 0;
}
.settings-card :deep(.setting-input) {
  border: 1px solid var(--border);
  background: var(--bg-input);
  color: var(--text-heading);
  transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
}
.settings-card :deep(.setting-input:focus) {
  border-color: var(--color-accent);
  background: var(--bg-card);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-accent) 14%, transparent);
}
.setting-label h4,
.array-editor h4 {
  color: var(--text-heading);
  font-size: 0.88rem;
  font-weight: 900;
}
.setting-label p {
  margin-top: 0.2rem;
  color: var(--text-muted);
  font-size: 0.76rem;
}
.array-help,
.settings-card :deep(.array-help) {
  margin-top: 0.2rem;
  color: var(--text-muted);
  font-size: 0.76rem;
}
.array-editor {
  display: grid;
  gap: 0.85rem;
}
.array-editor-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}
.array-editor-head button,
.array-remove {
  border-radius: 0.7rem;
  background: var(--color-accent);
  color: white;
  padding: 0.5rem 0.8rem;
  font-size: 0.78rem;
  font-weight: 900;
  cursor: pointer;
  transition: background 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease, filter 0.18s ease;
}
.array-remove {
  justify-self: end;
  border: 1px solid rgba(225, 29, 72, 0.22);
  background: rgba(225, 29, 72, 0.12);
  color: #f43f5e;
}
.array-remove:hover,
.settings-card :deep(.array-remove:hover) {
  border-color: rgba(225, 29, 72, 0.42);
  background: rgba(225, 29, 72, 0.18);
  color: #fb7185;
  box-shadow: inset 0 0 0 1px rgba(225, 29, 72, 0.08);
}
.array-remove:active,
.settings-card :deep(.array-remove:active) {
  background: rgba(225, 29, 72, 0.22);
  box-shadow: none;
  transform: scale(0.98);
}
.array-item {
  display: grid;
  gap: 0.7rem;
  border: 1px solid var(--border);
  border-radius: 0.9rem;
  background: color-mix(in srgb, var(--bg-input) 72%, transparent);
  padding: 0.9rem;
}
.array-field,
.service-field,
.settings-card :deep(.array-field),
.settings-card :deep(.service-field) {
  display: grid;
  gap: 0.35rem;
}
.array-field span,
.service-field span,
.settings-card :deep(.array-field span),
.settings-card :deep(.service-field span) {
  color: var(--text-heading);
  font-size: 0.76rem;
  font-weight: 900;
}
.service-field small,
.settings-card :deep(.service-field small) {
  color: var(--text-muted);
  font-size: 0.7rem;
}
.settings-card :deep(.service-editor) {
  gap: 1.1rem;
  border: 1px solid var(--border);
  border-radius: 1.1rem;
  background:
    linear-gradient(135deg, color-mix(in srgb, var(--color-accent) 8%, transparent), transparent 38%),
  color-mix(in srgb, var(--bg-card) 92%, var(--bg-input));
  padding: 1rem;
}
.settings-card :deep(.service-editor .array-editor-head) {
  align-items: flex-start;
}
.settings-card :deep(.service-editor-title) {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  width: 100%;
  min-width: 0;
  gap: 0.85rem;
}
.settings-card :deep(.service-count) {
  flex: 0 0 auto;
  border: 1px solid color-mix(in srgb, var(--color-accent) 24%, var(--border));
  border-radius: 999px;
  background: color-mix(in srgb, var(--color-accent) 10%, var(--bg-card));
  color: var(--color-accent);
  padding: 0.3rem 0.6rem;
  font-size: 0.7rem;
  font-weight: 900;
  line-height: 1;
}
.settings-card :deep(.service-add-btn) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  margin-top: 0.75rem;
  min-height: 2.35rem;
  border-radius: 0.75rem;
  background: var(--color-accent);
  color: white;
  padding: 0.55rem 0.85rem;
  font-size: 0.78rem;
  font-weight: 900;
  box-shadow: 0 10px 24px color-mix(in srgb, var(--color-accent) 20%, transparent);
  transition: transform 0.18s ease, filter 0.18s ease;
}
.settings-card :deep(.service-add-btn:hover) {
  filter: brightness(1.04);
  transform: translateY(-1px);
}
.settings-card :deep(.service-add-btn .material-symbols-outlined) {
  font-size: 1.05rem;
}
.settings-card :deep(.service-suggestion-row) {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  overflow-x: auto;
  border: 1px solid var(--border);
  border-radius: 0.9rem;
  background: color-mix(in srgb, var(--bg-input) 70%, transparent);
  padding: 0.55rem;
}
.settings-card :deep(.service-suggestion-row > span:first-child) {
  flex: 0 0 auto;
  color: var(--text-muted);
  font-size: 0.72rem;
  font-weight: 900;
}
.settings-card :deep(.service-suggestion-row button) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  flex: 0 0 auto;
  border: 1px solid var(--border);
  border-radius: 0.65rem;
  background: var(--bg-card);
  color: var(--text-heading);
  transition: border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
}
.settings-card :deep(.service-suggestion-row button:hover) {
  border-color: var(--color-accent);
  color: var(--color-accent);
  transform: translateY(-1px);
}
.settings-card :deep(.service-suggestion-row .material-symbols-outlined) {
  font-size: 1.12rem;
}
.settings-card :deep(.service-card-list) {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}
.settings-card :deep(.supporter-card-list) {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
.settings-card :deep(.stats-card-list) {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.9rem;
}
.settings-card :deep(.strength-card-list) {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}
.settings-card :deep(.flow-card-list),
.settings-card :deep(.testimonial-card-list) {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.9rem;
}
.settings-card :deep(.service-card-editor) {
  display: grid;
  gap: 1rem;
  border: 1px solid var(--border);
  border-radius: 1rem;
  background: color-mix(in srgb, var(--bg-card) 94%, var(--bg-input));
  padding: 1rem;
  box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
  transition: border-color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
}
.settings-card :deep(.supporter-card-editor) {
  gap: 0.85rem;
}
.settings-card :deep(.stats-card-editor) {
  align-content: start;
  gap: 0.85rem;
}
.settings-card :deep(.strength-card-editor) {
  gap: 1rem;
}
.settings-card :deep(.flow-card-editor),
.settings-card :deep(.testimonial-card-editor) {
  gap: 1rem;
}
.settings-card :deep(.flow-step-preview) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.7rem;
  height: 2.7rem;
  flex: 0 0 auto;
  border: 1px solid color-mix(in srgb, var(--color-accent) 24%, var(--border));
  border-radius: 0.85rem;
  background: color-mix(in srgb, var(--color-accent) 12%, var(--bg-card));
  color: var(--color-accent);
  font-size: 0.85rem;
  font-weight: 950;
}
.settings-card :deep(.flow-field-grid) {
  grid-template-columns: 0.45fr 1fr;
}
.settings-card :deep(.testimonial-preview) {
  display: grid;
  gap: 0.45rem;
  border: 1px solid color-mix(in srgb, var(--color-accent) 22%, var(--border));
  border-radius: 0.9rem;
  background:
    linear-gradient(135deg, color-mix(in srgb, var(--color-accent) 12%, transparent), transparent 62%),
    var(--bg-input);
  padding: 0.95rem;
}
.settings-card :deep(.testimonial-preview > .material-symbols-outlined) {
  color: var(--color-accent);
  font-size: 1.35rem;
}
.settings-card :deep(.testimonial-preview p) {
  color: var(--text-body);
  font-size: 0.82rem;
  line-height: 1.55;
}
.settings-card :deep(.testimonial-preview strong) {
  color: var(--text-heading);
  font-size: 0.86rem;
  font-weight: 900;
}
.settings-card :deep(.testimonial-preview small) {
  color: var(--text-muted);
  font-size: 0.74rem;
  font-weight: 800;
}
.settings-card :deep(.vision-admin-grid) {
  display: grid;
  grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.15fr);
  gap: 1rem;
}
.settings-card :deep(.vision-admin-panel),
.settings-card :deep(.mission-admin-panel) {
  align-content: start;
}
.settings-card :deep(.mission-admin-panel) {
  margin-top: 0.45rem;
}
.settings-card :deep(.mission-admin-list) {
  display: grid;
  gap: 0.9rem;
  padding-top: 0.25rem;
}
.settings-card :deep(.mission-admin-card) {
  gap: 1rem;
}
.settings-card :deep(.mission-number-icon) {
  font-family: inherit;
  font-size: 0.82rem;
  font-weight: 950;
  letter-spacing: 0;
}
.settings-card :deep(.footer-settings-grid) {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}
.settings-card :deep(.footer-panel) {
  display: grid;
  gap: 1rem;
  align-content: start;
  border: 1px solid var(--border);
  border-radius: 1rem;
  background: color-mix(in srgb, var(--bg-card) 94%, var(--bg-input));
  padding: 1rem;
  transition: border-color 0.18s ease, box-shadow 0.18s ease;
}
.settings-card :deep(.footer-panel:hover) {
  border-color: color-mix(in srgb, var(--color-accent) 28%, var(--border));
  box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
}
.settings-card :deep(.footer-panel-wide) {
  grid-column: 1 / -1;
}
.settings-card :deep(.footer-panel-head) {
  display: flex;
  gap: 0.75rem;
  align-items: flex-start;
}
.settings-card :deep(.footer-panel-head > .material-symbols-outlined) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.35rem;
  height: 2.35rem;
  flex: 0 0 auto;
  border-radius: 0.8rem;
  background: color-mix(in srgb, var(--color-accent) 12%, var(--bg-card));
  color: var(--color-accent);
  font-size: 1.25rem;
}
.settings-card :deep(.footer-panel-head h4) {
  color: var(--text-heading);
  font-size: 0.92rem;
  font-weight: 950;
}
.settings-card :deep(.footer-panel-head p) {
  margin-top: 0.18rem;
  color: var(--text-muted);
  font-size: 0.76rem;
}
.settings-card :deep(.footer-field-grid) {
  display: grid;
  gap: 0.9rem;
}
.settings-card :deep(.footer-panel .setting-row) {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.45rem;
}
.settings-card :deep(.footer-panel .setting-label) {
  width: auto;
  padding-top: 0;
}
.settings-card :deep(.footer-link-editor) {
  display: grid;
  gap: 0.7rem;
}
.settings-card :deep(.footer-link-head) {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.8rem;
}
.settings-card :deep(.footer-link-head h5) {
  color: var(--text-heading);
  font-size: 0.8rem;
  font-weight: 950;
}
.settings-card :deep(.footer-link-head button) {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  border-radius: 0.65rem;
  background: var(--color-accent);
  color: var(--text-btn);
  padding: 0.45rem 0.65rem;
  font-size: 0.72rem;
  font-weight: 900;
  cursor: pointer;
}
.settings-card :deep(.footer-link-head .material-symbols-outlined) {
  font-size: 0.95rem;
}
.settings-card :deep(.footer-link-list) {
  display: grid;
  gap: 0.65rem;
}
.settings-card :deep(.footer-link-item) {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr) minmax(0, 1.15fr) auto;
  gap: 0.6rem;
  align-items: end;
  border: 1px solid var(--border);
  border-radius: 0.85rem;
  background: color-mix(in srgb, var(--bg-input) 76%, transparent);
  padding: 0.7rem;
}
.settings-card :deep(.footer-link-index) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  margin-bottom: 0.1rem;
  border-radius: 999px;
  background: var(--bg-card);
  color: var(--text-muted);
  font-size: 0.7rem;
  font-weight: 950;
}
.settings-card :deep(.footer-link-remove) {
  margin-bottom: 0.05rem;
}
.settings-card :deep(.stats-card-preview) {
  display: grid;
  gap: 0.25rem;
  border: 1px solid color-mix(in srgb, var(--color-accent) 22%, var(--border));
  border-radius: 0.9rem;
  background:
    linear-gradient(135deg, color-mix(in srgb, var(--color-accent) 14%, transparent), transparent 62%),
    var(--bg-input);
  padding: 0.9rem;
  min-height: 5.5rem;
}
.settings-card :deep(.stats-card-number) {
  color: var(--text-heading);
  font-size: 1.75rem;
  font-weight: 950;
  line-height: 1;
}
.settings-card :deep(.stats-card-preview small) {
  color: var(--text-muted);
  font-size: 0.75rem;
  font-weight: 800;
}
.settings-card :deep(.stats-field-grid) {
  gap: 0.7rem;
}
.settings-card :deep(.stats-remove) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  width: 100%;
  cursor: pointer;
}
.settings-card :deep(.stats-remove .material-symbols-outlined) {
  font-size: 1rem;
}
.settings-card :deep(.supporter-mode-toggle) {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.45rem;
  border: 1px solid var(--border);
  border-radius: 0.75rem;
  background: var(--bg-input);
  padding: 0.3rem;
}
.settings-card :deep(.supporter-mode-toggle button) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  min-height: 2.1rem;
  border-radius: 0.55rem;
  color: var(--text-muted);
  cursor: pointer;
  font-size: 0.76rem;
  font-weight: 900;
  transition: background 0.18s ease, color 0.18s ease;
}
.settings-card :deep(.supporter-mode-toggle button.active) {
  background: var(--color-accent);
  color: var(--text-btn);
}
.settings-card :deep(.supporter-mode-toggle .material-symbols-outlined) {
  font-size: 1rem;
}
.settings-card :deep(.supporter-logo-upload) {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
.settings-card :deep(.supporter-logo-preview) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 4rem;
  height: 4rem;
  flex: 0 0 auto;
  overflow: hidden;
  border: 1px dashed color-mix(in srgb, var(--color-accent) 42%, var(--border));
  border-radius: 0.85rem;
  background: color-mix(in srgb, var(--bg-input) 78%, transparent);
  color: var(--color-accent);
}
.settings-card :deep(.supporter-logo-preview.sm) {
  width: 2.45rem;
  height: 2.45rem;
  border-style: solid;
  border-radius: 0.85rem;
}
.settings-card :deep(.supporter-logo-preview img) {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 0.35rem;
}
.settings-card :deep(.supporter-upload-btn) {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  width: fit-content;
  padding: 0.55rem 0.8rem;
  font-size: 0.76rem;
}
.settings-card :deep(.service-card-editor:hover) {
  border-color: color-mix(in srgb, var(--color-accent) 34%, var(--border));
  box-shadow: 0 18px 42px rgba(15, 23, 42, 0.09);
  transform: translateY(-1px);
}
.settings-card :deep(.service-card-head) {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}
.settings-card :deep(.service-card-title) {
  display: flex;
  align-items: center;
  min-width: 0;
  gap: 0.7rem;
}
.settings-card :deep(.service-card-number) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.05rem;
  height: 2.05rem;
  flex: 0 0 auto;
  border-radius: 999px;
  background: color-mix(in srgb, var(--bg-input) 80%, transparent);
  color: var(--text-muted);
  font-size: 0.72rem;
  font-weight: 900;
}
.settings-card :deep(.service-card-title strong) {
  display: block;
  color: var(--text-heading);
  font-size: 0.9rem;
  font-weight: 900;
}
.settings-card :deep(.service-card-title small) {
  display: block;
  max-width: 20rem;
  overflow: hidden;
  color: var(--text-muted);
  font-size: 0.72rem;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.settings-card :deep(.service-card-icon) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.45rem;
  height: 2.45rem;
  flex: 0 0 auto;
  border-radius: 0.85rem;
  background: color-mix(in srgb, var(--color-accent) 15%, var(--bg-card));
  color: var(--color-accent);
  font-size: 1.45rem;
}
.settings-card :deep(.service-field-grid) {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.85rem;
  align-items: start;
}
.settings-card :deep(.service-field.full) {
  grid-column: auto;
}
.settings-card :deep(.service-input) {
  min-height: 42px;
  padding-top: 0;
  padding-bottom: 0;
}
.settings-card :deep(textarea.service-input) {
  min-height: 96px;
  padding-top: 0.7rem;
  padding-bottom: 0.7rem;
}
.settings-card :deep(.service-icon-select) {
  min-height: 42px;
}
.settings-card :deep(.service-icon-select .multiselect__tags) {
  min-height: 42px;
  border: 1px solid var(--border);
  border-radius: 0.5rem;
  background: var(--bg-input);
  padding: 7px 40px 0 12px;
}
.settings-card :deep(.service-icon-select.multiselect--active .multiselect__tags) {
  border-color: var(--color-accent);
  background: var(--bg-card);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-accent) 14%, transparent);
}
.settings-card :deep(.service-icon-select .multiselect__single),
.settings-card :deep(.service-icon-select .multiselect__input),
.settings-card :deep(.service-icon-select .multiselect__placeholder) {
  min-height: 24px;
  margin: 0;
  background: transparent;
  color: var(--text-heading);
  font-size: 0.875rem;
  line-height: 24px;
}
.settings-card :deep(.service-icon-select .multiselect__placeholder) {
  color: var(--text-muted);
}
.settings-card :deep(.service-icon-select .multiselect__content-wrapper) {
  border-color: var(--border);
  background: var(--bg-card);
}
.settings-card :deep(.service-icon-select .multiselect__option--highlight) {
  background: var(--color-accent);
}
.settings-card :deep(.icon-select-option) {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}
.settings-card :deep(.icon-select-option .material-symbols-outlined) {
  color: var(--color-accent);
  font-size: 1.15rem;
}
.settings-card :deep(.icon-remove) {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.2rem;
  height: 2.2rem;
  padding: 0;
  border-radius: 0.7rem;
}
.settings-card :deep(.icon-remove .material-symbols-outlined) {
  font-size: 1rem;
}
.settings-card :deep(.service-empty) {
  grid-column: 1 / -1;
  display: grid;
  justify-items: center;
  gap: 0.45rem;
  border: 1px dashed color-mix(in srgb, var(--color-accent) 35%, var(--border));
  border-radius: 1rem;
  background: color-mix(in srgb, var(--bg-input) 70%, transparent);
  padding: 2rem 1rem;
  text-align: center;
}
.settings-card :deep(.service-empty > .material-symbols-outlined) {
  color: var(--color-accent);
  font-size: 2rem;
}
.settings-card :deep(.service-empty strong) {
  color: var(--text-heading);
  font-size: 0.95rem;
  font-weight: 900;
}
@media (max-width: 767px) {
  .setting-row {
    grid-template-columns: 1fr;
  }
  .array-editor-head,
  .settings-card :deep(.array-editor-head),
  .service-card-head,
  .settings-card :deep(.service-card-head) {
    align-items: flex-start;
    flex-direction: column;
  }
  .settings-card :deep(.service-editor-title) {
    flex-direction: column;
  }
  .settings-card :deep(.service-card-list),
  .settings-card :deep(.supporter-card-list),
  .settings-card :deep(.stats-card-list),
  .settings-card :deep(.strength-card-list),
  .settings-card :deep(.flow-card-list),
  .settings-card :deep(.testimonial-card-list),
  .settings-card :deep(.footer-settings-grid),
  .settings-card :deep(.vision-admin-grid),
  .settings-card :deep(.flow-field-grid),
  .settings-card :deep(.service-field-grid) {
    grid-template-columns: 1fr;
  }
  .settings-card :deep(.footer-link-item) {
    grid-template-columns: 1fr;
  }
  .settings-card :deep(.mission-admin-panel) {
    margin-top: 0;
  }
  .settings-card :deep(.icon-remove) {
    width: 100%;
  }
}
</style>

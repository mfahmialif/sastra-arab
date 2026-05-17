<template>
  <footer class="public-footer">
    <div class="footer-glow"></div>

    <div class="relative mx-auto max-w-7xl px-5 py-14 lg:px-8">
      <div class="footer-main">
        <div class="footer-brand">
          <router-link to="/" class="footer-logo">
            <span class="footer-logo-icon">
              <span class="material-symbols-outlined text-[26px]">auto_stories</span>
            </span>
            <span>
              <span class="block text-xl font-black text-white">{{ footer.brand_title }}</span>
              <span class="block text-xs font-black uppercase tracking-[0.2em] text-sky-300">{{ footer.brand_subtitle }}</span>
            </span>
          </router-link>
          <p class="mt-6 max-w-xl text-base leading-8 text-slate-300">
            {{ footer.description }}
          </p>
          <div class="mt-7 flex flex-wrap gap-3">
            <component :is="linkComponent(footer.primary_href)" v-bind="linkAttrs(footer.primary_href)" class="footer-cta">
              {{ footer.primary_label }}
              <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </component>
            <component :is="linkComponent(footer.secondary_href)" v-bind="linkAttrs(footer.secondary_href)" class="footer-ghost">
              {{ footer.secondary_label }}
            </component>
          </div>
        </div>

        <div class="footer-column">
          <p class="footer-heading">{{ footer.navigation_title }}</p>
          <component
            :is="linkComponent(item.href)"
            v-for="item in footer.navigation"
            :key="`${item.label}-${item.href}`"
            v-bind="linkAttrs(item.href)"
            class="footer-link"
          >
            {{ item.label }}
          </component>
        </div>

        <div class="footer-column">
          <p class="footer-heading">{{ footer.services_title }}</p>
          <component
            :is="linkComponent(item.href)"
            v-for="item in footer.services"
            :key="`${item.label}-${item.href}`"
            v-bind="linkAttrs(item.href)"
            class="footer-link"
          >
            {{ item.label }}
          </component>
        </div>

        <div class="footer-contact">
          <p class="footer-heading">{{ footer.contact_title }}</p>
          <a :href="`mailto:${footer.email}`" class="footer-contact-row">
            <span class="material-symbols-outlined">mail</span>
            {{ footer.email }}
          </a>
          <a :href="whatsappHref" target="_blank" rel="noopener" class="footer-contact-row">
            <span class="material-symbols-outlined">chat</span>
            {{ footer.phone }}
          </a>
          <a :href="footer.maps_href" target="_blank" rel="noopener" class="footer-contact-row">
            <span class="material-symbols-outlined">location_on</span>
            {{ footer.address }}
          </a>
        </div>
      </div>

      <div class="footer-bottom">
        <p>© {{ year }} {{ footer.copyright }}</p>
        <div class="flex flex-wrap items-center gap-4">
          <component :is="linkComponent(footer.admin_href)" v-bind="linkAttrs(footer.admin_href)" class="footer-bottom-link">{{ footer.admin_label }}</component>
          <a :href="footer.maps_href" target="_blank" rel="noopener" class="footer-bottom-link">{{ footer.maps_label }}</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
import { defaultLandingSettings } from '../../../views/public/landing/settings'

const props = defineProps({
  year: {
    type: Number,
    required: true,
  },
  content: {
    type: Object,
    default: () => defaultLandingSettings.footer,
  },
})

const footer = computed(() => ({
  ...defaultLandingSettings.footer,
  ...(props.content || {}),
  navigation: Array.isArray(props.content?.navigation) ? props.content.navigation : defaultLandingSettings.footer.navigation,
  services: Array.isArray(props.content?.services) ? props.content.services : defaultLandingSettings.footer.services,
}))

const whatsappHref = computed(() => {
  const value = footer.value.whatsapp || footer.value.phone || ''
  const number = value.replace(/\D/g, '')
  return number ? `https://wa.me/${number}` : '#'
})

function isExternal(href = '') {
  return /^https?:\/\//.test(href) || href.startsWith('mailto:') || href.startsWith('tel:')
}

function linkComponent(href = '') {
  return isExternal(href) ? 'a' : 'router-link'
}

function linkAttrs(href = '') {
  if (isExternal(href)) {
    return { href, target: '_blank', rel: 'noopener' }
  }

  return { to: href || '/' }
}
</script>

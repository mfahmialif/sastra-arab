<template>
  <section id="home" class="relative overflow-hidden pt-30">
    <div class="hero-glow"></div>
    <div class="mx-auto grid min-h-screen max-w-7xl items-center gap-12 px-5 pb-16 pt-8 lg:grid-cols-[1fr_0.95fr] lg:px-8 lg:pt-16">
      <div class="reveal-up">
        <p class="mb-7 inline-flex items-center gap-3 rounded-full bg-slate-200/70 px-5 py-2 text-sm font-black tracking-[0.12em] text-slate-600 shadow-sm">
          <span class="size-2.5 rounded-full bg-sky-700"></span>
          {{ hero.eyebrow }}
        </p>
        <h1 class="max-w-4xl text-5xl font-black leading-[1.02] tracking-tight text-[#101418] sm:text-6xl lg:text-7xl">
          {{ hero.main_title }}
        </h1>
        <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-600">
          {{ hero.description }}
        </p>
        <div class="mt-9 flex flex-wrap items-center gap-4">
          <a :href="hero.primary_href" class="cta-primary">
            {{ hero.primary_label }}
            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
          </a>
          <a :href="hero.secondary_href" class="cta-secondary">
            {{ hero.secondary_label }}
            <span class="material-symbols-outlined text-[20px]">groups</span>
          </a>
        </div>
      </div>

      <div class="hero-card reveal-up delay-2">
        <div class="hero-photo-frame">
          <div v-if="isHeroLoading || !heroImage" class="hero-photo-skeleton" aria-hidden="true">
            <span></span>
          </div>
          <img v-else :src="heroImage" :alt="heroAlt" class="hero-photo-image" />
          <div v-if="hasHeroText" class="hero-photo-caption">
            <div>
              <p v-if="hero.label">{{ hero.label }}</p>
              <h2 v-if="hero.title">{{ hero.title }}</h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { backendAssetUrl } from '../../../utils/asset'

const props = defineProps({
  hero: {
    type: Object,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const hero = computed(() => props.hero)
const heroImage = computed(() => backendAssetUrl(props.hero.image_path || props.hero.image_url))
const heroAlt = computed(() => props.hero.image_alt || 'Kegiatan Program Studi Sastra Arab')
const hasHeroText = computed(() => props.hero.label || props.hero.title)
const isHeroLoading = computed(() => props.loading)
</script>

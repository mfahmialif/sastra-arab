<template>
  <main>
    <div v-if="landingLoading" class="landing-skeleton" aria-hidden="true">
      <section id="home" class="relative overflow-hidden pt-30">
        <div class="hero-glow"></div>
        <div class="mx-auto grid min-h-screen max-w-7xl items-center gap-12 px-5 pb-16 pt-8 lg:grid-cols-[1fr_0.95fr] lg:px-8 lg:pt-16">
          <div>
            <div class="sk sk-pill mb-7"></div>
            <div class="sk sk-title"></div>
            <div class="sk sk-title short"></div>
            <div class="mt-7 max-w-2xl space-y-3">
              <div class="sk sk-line"></div>
              <div class="sk sk-line wide"></div>
              <div class="sk sk-line small"></div>
            </div>
            <div class="mt-9 flex flex-wrap items-center gap-4">
              <div class="sk sk-button"></div>
              <div class="sk sk-button secondary"></div>
            </div>
          </div>

          <div class="hero-card">
            <div class="hero-photo-frame">
              <div class="hero-photo-skeleton">
                <span></span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section v-for="section in skeletonSections" :key="section.key" :class="section.class">
        <div class="mx-auto max-w-7xl px-5 py-18 lg:px-8 lg:py-24">
          <div class="section-head">
            <div class="w-full max-w-3xl">
              <div class="sk sk-kicker"></div>
              <div class="sk sk-heading mt-4"></div>
            </div>
            <div v-if="section.action" class="sk sk-circle"></div>
          </div>

          <div v-if="section.type === 'vision'" class="vision-mission-grid mt-10">
            <div class="sk sk-panel"></div>
            <div class="grid gap-3">
              <div v-for="item in 4" :key="item" class="sk sk-row"></div>
            </div>
          </div>

          <div v-else-if="section.type === 'supporter'" class="mt-10 overflow-hidden">
            <div class="flex gap-4">
              <div v-for="item in 6" :key="item" class="sk sk-logo"></div>
            </div>
          </div>

          <div v-else-if="section.type === 'news'" class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div v-for="item in 3" :key="item" class="sk-card">
              <div class="sk sk-image"></div>
              <div class="space-y-3 p-5">
                <div class="sk sk-line tiny"></div>
                <div class="sk sk-line"></div>
                <div class="sk sk-line wide"></div>
              </div>
            </div>
          </div>

          <div v-else-if="section.type === 'cta'" class="cta-band mt-8">
            <div class="space-y-4">
              <div class="sk sk-kicker dark"></div>
              <div class="sk sk-heading dark"></div>
            </div>
            <div class="sk sk-button light"></div>
          </div>

          <div v-else-if="section.type === 'contact'" class="contact-panel mt-10">
            <div class="space-y-4">
              <div class="sk sk-heading"></div>
              <div class="sk sk-line wide"></div>
              <div class="sk sk-line small"></div>
            </div>
            <div class="grid gap-3">
              <div v-for="item in 3" :key="item" class="sk sk-row compact"></div>
            </div>
          </div>

          <div v-else class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div v-for="item in section.items" :key="item" class="sk-card p-6">
              <div class="sk sk-icon"></div>
              <div class="sk sk-line mt-6"></div>
              <div class="sk sk-line wide mt-3"></div>
              <div class="sk sk-line small mt-3"></div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <template v-else>
      <Hero :hero="landing.hero" />
      <VisionMission :content="landing.visionMission" />
      <Services :services="landing.services.items" :content="landing.services" />
      <Supporter :supporters="landing.supporters.items" :content="landing.supporters" />
      <NewsUpdate :updates="latestNews" :content="landing.news" />
      <About :stats="landing.about.stats" :content="landing.about" />
      <PublishCta :content="landing.publishCta" />
      <Strengths :strengths="landing.strengths.items" :content="landing.strengths" />
      <Flow :publishing-flow="landing.flow.items" :content="landing.flow" />
      <Testimonials :testimonials="landing.testimonials.items" :content="landing.testimonials" />
      <Contact :content="landing.contact" />
    </template>
  </main>
</template>

<script setup>
import { nextTick, onMounted, ref } from 'vue'
import AOS from 'aos'
import 'aos/dist/aos.css'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import './styles.css'
import api from '../../../axios'
import About from './_About.vue'
import Contact from './_Contact.vue'
import Flow from './_Flow.vue'
import Hero from './_Hero.vue'
import NewsUpdate from './_NewsUpdate.vue'
import PublishCta from './_PublishCta.vue'
import Services from './_Services.vue'
import Strengths from './_Strengths.vue'
import Supporter from './_Supporter.vue'
import Testimonials from './_Testimonials.vue'
import VisionMission from './_VisionMission.vue'
import { updates } from './data'
import { defaultLandingSettings, normalizeLandingSettings } from './settings'
import { newsDetailPath, normalizeNews } from '../news/utils'
import { applySeo } from '../../../utils/seo'

const latestNews = ref(updates)
const landing = ref(normalizeLandingSettings(defaultLandingSettings))
const landingLoading = ref(true)
const skeletonSections = [
  { key: 'vision', type: 'vision', class: 'bg-white' },
  { key: 'services', items: 3, class: 'bg-slate-50' },
  { key: 'supporter', type: 'supporter', class: 'bg-white' },
  { key: 'news', type: 'news', action: true, class: 'news-update-section' },
  { key: 'about', items: 3, class: 'bg-white' },
  { key: 'cta', type: 'cta', class: 'bg-slate-50' },
  { key: 'strengths', items: 3, class: 'bg-white' },
  { key: 'flow', items: 4, class: 'bg-slate-50' },
  { key: 'testimonials', items: 3, class: 'bg-white' },
  { key: 'contact', type: 'contact', class: 'bg-slate-50' },
]

function newsItemCount() {
  const count = Number(landing.value.news.item_count)
  return Number.isFinite(count) ? Math.min(Math.max(Math.trunc(count), 1), 12) : 6
}

function formatNewsDate(dateStr) {
  if (!dateStr) return { day: '', month: '' }
  const date = new Date(dateStr)
  if (Number.isNaN(date.getTime())) return { day: '', month: '' }

  return {
    day: new Intl.DateTimeFormat('id-ID', { day: '2-digit' }).format(date),
    month: new Intl.DateTimeFormat('id-ID', { month: 'short' }).format(date),
  }
}

async function fetchLatestNews() {
  const itemCount = newsItemCount()

  try {
    const { data } = await api.get('/news', {
      params: {
        status: 'Published',
        per_page: itemCount,
        sort_by: 'created_at',
        sort_dir: 'desc',
      },
    })

    const items = (data.data || []).map((item) => {
      const normalized = normalizeNews(item)
      const date = formatNewsDate(normalized.created_at)

      return {
        id: normalized.id,
        slug: normalized.slug,
        href: newsDetailPath(normalized),
        day: date.day,
        month: date.month,
        category: normalized.categoryName,
        title: normalized.title,
        author: normalized.authorName,
        body: normalized.excerpt,
        image: normalized.image,
      }
    })

    latestNews.value = items.length ? items : landing.value.news.fallback_items.slice(0, itemCount)
  } catch {
    latestNews.value = landing.value.news.fallback_items.slice(0, itemCount)
  }
}

async function fetchLandingSettings() {
  landingLoading.value = true
  try {
    const { data } = await api.get('/settings/landing', {
      params: { _t: Date.now() },
    })
    landing.value = normalizeLandingSettings(data)
  } catch {
    landing.value = normalizeLandingSettings(defaultLandingSettings)
  } finally {
    landingLoading.value = false
  }
}

onMounted(async () => {
  AOS.init({
    duration: 850,
    easing: 'ease-out-cubic',
    once: true,
    offset: 90,
  })
  await fetchLandingSettings()
  applySeo({
    title: 'Sastra Arab',
    description: landing.value.hero.description,
    image: landing.value.hero.image_url || '/img/news/news1.jpg',
    url: window.location.href,
  })
  await fetchLatestNews()
  await nextTick()
  AOS.refreshHard()
})
</script>

<style scoped>
.landing-skeleton {
  pointer-events: none;
}

.sk,
.sk-card {
  position: relative;
  overflow: hidden;
  border-radius: 1rem;
  background: linear-gradient(135deg, rgba(226, 232, 240, 0.86), rgba(248, 250, 252, 0.96), rgba(226, 232, 240, 0.78));
}

.sk::after,
.sk-card::after {
  content: '';
  position: absolute;
  inset: 0;
  transform: translateX(-100%);
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.68), transparent);
  animation: skeletonSweep 1.45s ease-in-out infinite;
}

.sk-pill {
  width: 13rem;
  height: 2.55rem;
  border-radius: 999px;
}

.sk-title {
  width: min(42rem, 92%);
  height: clamp(3.1rem, 7vw, 5rem);
  border-radius: 1.2rem;
}

.sk-title.short {
  width: min(24rem, 68%);
  margin-top: 0.9rem;
}

.sk-line {
  width: 74%;
  height: 1rem;
  border-radius: 999px;
}

.sk-line.wide {
  width: 92%;
}

.sk-line.small {
  width: 54%;
}

.sk-line.tiny {
  width: 38%;
  height: 0.82rem;
}

.sk-button {
  width: 10.5rem;
  height: 3.1rem;
  border-radius: 999px;
}

.sk-button.secondary {
  width: 9.5rem;
}

.sk-button.light,
.sk-heading.dark,
.sk-kicker.dark {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), rgba(125, 211, 252, 0.16), rgba(255, 255, 255, 0.1));
}

.sk-kicker {
  width: 8.5rem;
  height: 0.9rem;
  border-radius: 999px;
}

.sk-heading {
  width: min(38rem, 100%);
  height: 3.35rem;
  border-radius: 1rem;
}

.sk-circle {
  flex: 0 0 auto;
  width: 3.1rem;
  height: 3.1rem;
  border-radius: 999px;
}

.sk-panel {
  min-height: 23rem;
  border-radius: 1.75rem;
}

.sk-row {
  height: 5.25rem;
  border-radius: 1.25rem;
}

.sk-row.compact {
  height: 3.75rem;
}

.sk-logo {
  flex: 0 0 13.5rem;
  height: 4rem;
  border-radius: 999px;
}

.sk-card {
  min-height: 13rem;
  border: 1px solid rgb(226 232 240);
  background-color: white;
  box-shadow: 0 16px 52px rgba(15, 23, 42, 0.06);
}

.sk-image {
  height: 14.5rem;
  border-radius: 1rem 1rem 0 0;
}

.sk-icon {
  width: 3.15rem;
  height: 3.15rem;
  border-radius: 1rem;
}

.public-root[data-theme='dark'] .sk,
.public-root[data-theme='dark'] .sk-card {
  border-color: rgba(125, 211, 252, 0.16);
  background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(8, 17, 31, 0.96), rgba(15, 23, 42, 0.78));
}

.public-root[data-theme='dark'] .sk::after,
.public-root[data-theme='dark'] .sk-card::after {
  background: linear-gradient(90deg, transparent, rgba(125, 211, 252, 0.12), transparent);
}

@media (max-width: 767px) {
  .sk-heading {
    height: 4.75rem;
  }

  .sk-circle {
    display: none;
  }
}
</style>

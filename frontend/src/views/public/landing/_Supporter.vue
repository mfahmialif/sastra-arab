<template>
  <section class="supporter-section bg-white py-16">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
      <div class="mx-auto max-w-3xl text-center" data-aos="fade-up">
        <p class="section-kicker">{{ content.kicker }}</p>
        <h2 class="section-title mx-auto mt-4">{{ content.title }}</h2>
      </div>
    </div>
    <div class="supporter-marquee mt-10" data-aos="fade-up" data-aos-delay="120">
      <div class="supporter-track">
        <div v-for="supporter in supporterLoop" :key="supporter.key" class="supporter-logo">
          <img v-if="supporter.display_type === 'logo' && supporter.logoSrc" :src="supporter.logoSrc" :alt="supporter.name" />
          <span v-else-if="supporter.display_type !== 'text'" class="material-symbols-outlined">{{ supporter.icon }}</span>
          <strong>{{ supporter.name }}</strong>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { backendAssetUrl } from '../../../utils/asset'

const props = defineProps({
  supporters: {
    type: Array,
    required: true,
  },
  content: {
    type: Object,
    required: true,
  },
})

const supporterLoop = computed(() => [...props.supporters, ...props.supporters].map((supporter, index) => ({
  ...supporter,
  logoSrc: backendAssetUrl(supporter.logo_path || supporter.logo_url),
  key: `${supporter.name}-${index}`,
})))
</script>

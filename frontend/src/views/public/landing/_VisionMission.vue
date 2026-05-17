<template>
  <section id="vision-mission" class="vision-mission-section py-16">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
      <div class="section-head" data-aos="fade-up">
        <div>
          <p class="section-kicker">{{ content.kicker }}</p>
          <h2 class="section-title mt-4">{{ content.title }}</h2>
        </div>
      </div>

      <div class="vision-mission-grid mt-10">
        <article class="vision-panel" data-aos="fade-up" data-aos-delay="80">
          <span class="material-symbols-outlined">visibility</span>
          <p class="section-kicker">{{ content.vision_title }}</p>
          <h3>{{ content.vision_body }}</h3>
        </article>

        <div class="mission-list" data-aos="fade-up" data-aos-delay="140">
          <div class="mission-head">
            <p class="section-kicker">{{ content.mission_title }}</p>
          </div>
          <article v-for="(item, index) in content.items" :key="`${item.title}-${index}`" class="mission-card">
            <span class="mission-icon">{{ String(index + 1).padStart(2, '0') }}</span>
            <div>
              <h3>{{ item.title }}</h3>
              <p>{{ item.body }}</p>
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  content: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <section class="bg-[#f6f8f7] py-20">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
      <div class="cta-band" data-aos="zoom-in">
        <div>
          <p class="section-kicker text-sky-300">{{ content.kicker }}</p>
          <h2 class="mt-4 text-4xl font-black tracking-tight text-white md:text-5xl">
            {{ content.title }}
          </h2>
        </div>
        <a :href="content.button_href" class="inline-flex h-12 items-center justify-center rounded-full bg-sky-400 px-6 text-sm font-black text-[#0b1020] transition hover:-translate-y-0.5 hover:bg-sky-300">
          {{ content.button_label }}
        </a>
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

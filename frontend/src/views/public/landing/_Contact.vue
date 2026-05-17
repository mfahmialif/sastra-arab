<template>
  <section id="contact" class="bg-[#f6f8f7] py-20">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
      <div class="contact-panel" data-aos="fade-up">
        <div>
          <p class="section-kicker">{{ content.kicker }}</p>
          <h2 class="mt-4 text-4xl font-black tracking-tight text-[#101418] md:text-5xl">{{ content.title }}</h2>
          <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">
            {{ content.body }}
          </p>
        </div>
        <div class="contact-list">
          <p><span class="material-symbols-outlined">mail</span> {{ content.email }}</p>
          <p><span class="material-symbols-outlined">call</span> {{ content.phone }}</p>
          <p><span class="material-symbols-outlined">location_on</span> {{ content.address }}</p>
          <router-link to="/login" class="cta-primary mt-3">
            {{ content.login_label }}
            <span class="material-symbols-outlined text-[20px]">login</span>
          </router-link>
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

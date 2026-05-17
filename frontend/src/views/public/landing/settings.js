import {
  publishingFlow,
  missions,
  services,
  stats,
  strengths,
  supporters,
  testimonials,
  updates,
} from './data'

export const defaultLandingSettings = {
  hero: {
    image_path: '',
    image_url: '',
    image_alt: 'Kegiatan Program Studi Sastra Arab',
    eyebrow: 'PROGRAM STUDI',
    main_title: 'Sastra Arab',
    description: 'Website resmi Program Studi Sastra Arab untuk informasi akademik, berita, kegiatan mahasiswa, dan layanan komunikasi prodi.',
    primary_label: 'Lihat Akademik',
    primary_href: '#services',
    secondary_label: 'Kegiatan Prodi',
    secondary_href: '#flow',
    label: '',
    title: '',
  },
  visionMission: {
    kicker: 'Visi Misi',
    title: 'Arah pengembangan Program Studi Sastra Arab.',
    vision_title: 'Visi',
    vision_body: 'Menjadi program studi yang unggul dalam pengembangan bahasa, sastra, budaya, dan tradisi keilmuan Arab berbasis nilai keislaman dan kebutuhan masyarakat.',
    mission_title: 'Misi',
    items: missions,
  },
  services: {
    kicker: 'Akademik',
    title: 'Informasi dan layanan Program Studi Sastra Arab.',
    items: services,
  },
  supporters: {
    kicker: 'Supporter',
    title: 'Ekosistem yang mendukung gerakan literasi kampus.',
    items: supporters,
  },
  news: {
    kicker: 'Sastra Arab Update',
    title: 'Berita, kegiatan, dan informasi prodi.',
    description: 'Kanal update dipakai untuk mengabarkan agenda akademik, kegiatan mahasiswa, informasi layanan, dan pengumuman prodi.',
    button_label: 'Semua News',
    item_count: 6,
    fallback_items: updates,
  },
  about: {
    kicker: 'Tentang Kami',
    title: 'Mengembangkan kajian bahasa, sastra, dan budaya Arab.',
    body: 'Program Studi Sastra Arab berfokus pada penguasaan bahasa Arab, analisis sastra, pemahaman budaya, dan penguatan tradisi keilmuan. Website ini menjadi kanal informasi akademik, berita, kegiatan, dan layanan prodi.',
    stats,
  },
  publishCta: {
    kicker: 'Sastra Arab',
    title: 'Temukan informasi akademik dan kegiatan terbaru Program Studi Sastra Arab.',
    button_label: 'Hubungi Prodi',
    button_href: '#contact',
  },
  strengths: {
    kicker: 'Kenapa Sastra Arab?',
    title: 'Ruang akademik untuk bahasa, sastra, budaya, dan tradisi keilmuan Arab.',
    items: strengths,
  },
  flow: {
    kicker: 'Kegiatan Prodi',
    title: 'Pengalaman belajar yang terarah dan aktif.',
    items: publishingFlow,
  },
  testimonials: {
    kicker: 'Testimoni Klien',
    title: 'Cerita penulis yang tumbuh bersama press.',
    items: testimonials,
  },
  contact: {
    kicker: 'Contact',
    title: 'Hubungi Program Studi Sastra Arab.',
    body: 'Sampaikan kebutuhan informasi akademik, kegiatan mahasiswa, kerja sama, dan layanan komunikasi prodi.',
    email: 'sastraarab@uiidalwa.ac.id',
    phone: '+62 812 0000 0000',
    address: 'Kompleks UII Dalwa, Pasuruan',
    login_label: 'Admin Login',
  },
  footer: {
    brand_title: 'Sastra Arab',
    brand_subtitle: 'Program Studi',
    description: 'Website Program Studi Sastra Arab untuk informasi akademik, berita, kegiatan, dan layanan komunikasi prodi.',
    primary_label: 'Lihat News',
    primary_href: '/news',
    secondary_label: 'Kontak Prodi',
    secondary_href: '/contact',
    navigation_title: 'Navigasi',
    navigation: [
      { label: 'Home', href: '/' },
      { label: 'Profil', href: '/#about' },
      { label: 'Akademik', href: '/#services' },
      { label: 'News', href: '/news' },
      { label: 'Contact', href: '/contact' },
    ],
    services_title: 'Layanan',
    services: [
      { label: 'Informasi Akademik', href: '/#services' },
      { label: 'Kegiatan Mahasiswa', href: '/#flow' },
      { label: 'Berita Prodi', href: '/news' },
      { label: 'Kontak Prodi', href: '/contact' },
    ],
    contact_title: 'Kontak',
    email: 'sastraarab@uiidalwa.ac.id',
    phone: '+62 812 0000 0000',
    whatsapp: '6281200000000',
    address: 'Pasuruan, Jawa Timur',
    maps_href: 'https://maps.app.goo.gl/pNNU4dnCtuu7y4Qk6',
    copyright: 'Sastra Arab. All rights reserved.',
    admin_label: 'Admin Login',
    admin_href: '/login',
    maps_label: 'Maps',
  },
}

function clone(value) {
  return JSON.parse(JSON.stringify(value))
}

function mergeSection(defaultSection, section) {
  if (Array.isArray(defaultSection)) {
    return Array.isArray(section) ? section : clone(defaultSection)
  }

  if (!defaultSection || typeof defaultSection !== 'object') {
    return section ?? defaultSection
  }

  const merged = clone(defaultSection)
  Object.entries(section || {}).forEach(([key, value]) => {
    if (Array.isArray(merged[key])) {
      merged[key] = Array.isArray(value) ? value : merged[key]
    } else if (merged[key] && typeof merged[key] === 'object') {
      merged[key] = mergeSection(merged[key], value)
    } else {
      merged[key] = value ?? merged[key]
    }
  })

  return merged
}

export function normalizeLandingSettings(settings = {}) {
  const normalized = mergeSection(defaultLandingSettings, settings)
  normalized.supporters.items = (normalized.supporters.items || []).map((item) => ({
    display_type: ['icon', 'logo', 'text'].includes(item.display_type) ? item.display_type : 'icon',
    icon: item.icon || 'school',
    logo_path: item.logo_path || '',
    logo_url: item.logo_url || '',
    name: item.name || '',
  }))
  const newsCount = Number(normalized.news.item_count)
  normalized.news.item_count = Number.isFinite(newsCount)
    ? Math.min(Math.max(Math.trunc(newsCount), 1), 12)
    : 6
  return normalized
}

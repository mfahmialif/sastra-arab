<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['system_name', 'Sastra Arab'],
            ['accent_color', '#880c27'],
            ['favicon_path', ''],
            ['favicon_url', ''],

            ['navbar_brand_mode', 'text'],
            ['navbar_text_icon', 'school'],
            ['navbar_text_title', 'Sastra Arabb'],
            ['navbar_text_subtitle', 'Program Studi'],
            ['navbar_logo_path', ''],
            ['navbar_logo_url', ''],

            ['login_image_path', ''],
            ['login_image_url', ''],

            ['smtp_enabled', false, 'boolean'],
            ['smtp_host', ''],
            ['smtp_port', 587, 'integer'],
            ['smtp_username', ''],
            ['smtp_password', ''],
            ['smtp_encryption', 'tls'],
            ['smtp_from_address', 'sastraarab@uiidalwa.ac.id'],
            ['smtp_from_name', 'Sastra Arab'],

            ['google_login_enabled', false, 'boolean'],
            ['google_client_id', ''],
            ['google_client_secret', ''],
            ['google_hosted_domain', ''],
            ['google_auto_create_users', false, 'boolean'],

            ['hero_image_path', ''],
            ['hero_image_url', ''],
            ['hero_image_alt', 'HB segaf'],
            ['hero_label', 'Rektor UII Dalwa'],
            ['hero_title', 'Dr. Segaf Baharun, M.H.I'],
            ['hero_badge_title', 'Akademik aktif'],
            ['hero_badge_description', 'Informasi prodi, kegiatan, dan layanan mahasiswa'],

            ['landing_content', json_encode($this->landingContent(), JSON_UNESCAPED_UNICODE)],
        ];

        foreach ($settings as $setting) {
            AppSetting::setValue($setting[0], $setting[1], $setting[2] ?? 'string');
        }
    }

    private function landingContent(): array
    {
        return [
            'hero' => [
                'image_path' => '',
                'image_url' => '',
                'image_alt' => 'HB segaf',
                'eyebrow' => 'PROGRAM STUDI',
                'main_title' => 'Sastra Arab',
                'description' => 'Website resmi Program Studi Sastra Arab untuk informasi akademik, berita, kegiatan mahasiswa, dan layanan komunikasi prodi.',
                'primary_label' => 'Lihat Akademik',
                'primary_href' => '#services',
                'secondary_label' => 'Kegiatan Prodi',
                'secondary_href' => '#flow',
                'label' => 'Rektor UII Dalwa',
                'title' => 'Dr. Segaf Baharun, M.H.I',
            ],
            'visionMission' => [
                'kicker' => 'Visi Misi',
                'title' => 'Arah pengembangan Program Studi Sastra Arab.',
                'vision_title' => 'Visi',
                'vision_body' => 'Menjadi program studi yang unggul dalam pengembangan bahasa, sastra, budaya, dan tradisi keilmuan Arab berbasis nilai keislaman dan kebutuhan masyarakat.',
                'mission_title' => 'Misi',
                'items' => [
                    ['icon' => 'menu_book', 'title' => 'Pendidikan Bahasa dan Sastra', 'body' => 'Menyelenggarakan pembelajaran bahasa, sastra, dan budaya Arab yang kuat secara teori dan praktik.'],
                    ['icon' => 'science', 'title' => 'Riset Keilmuan Arab', 'body' => 'Mengembangkan penelitian bahasa, sastra, budaya, dan turats Arab yang relevan dengan kebutuhan akademik.'],
                    ['icon' => 'groups', 'title' => 'Pengabdian dan Kolaborasi', 'body' => 'Membangun jejaring akademik serta menerapkan keilmuan Sastra Arab dalam kegiatan masyarakat.'],
                ],
            ],
            'services' => [
                'kicker' => 'Akademik',
                'title' => 'Informasi dan layanan Program Studi Sastra Arab.',
                'items' => [
                    ['icon' => 'groups', 'title' => 'Informasi Akademik', 'body' => 'Akses informasi kurikulum, kegiatan kuliah, dan agenda akademik prodi.', 'href' => '#services'],
                    ['icon' => 'translate', 'title' => 'Kajian Bahasa Arab', 'body' => 'Pembelajaran bahasa, sastra, linguistik, dan tradisi keilmuan Arab.', 'href' => '#about'],
                    ['icon' => 'groups', 'title' => 'Kegiatan Mahasiswa', 'body' => 'Ikuti kabar seminar, diskusi ilmiah, komunitas, dan aktivitas kemahasiswaan.', 'href' => '#flow'],
                    ['icon' => 'support_agent', 'title' => 'Layanan Prodi', 'body' => 'Hubungi pengelola prodi untuk kebutuhan administrasi dan informasi studi.', 'href' => '#contact'],
                    ['icon' => 'support_agent', 'title' => 'tes', 'body' => 'tes card', 'href' => '#tes'],
                ],
            ],
            'supporters' => [
                'kicker' => 'Supporter',
                'title' => 'Ekosistem yang mendukung gerakan literasi kampus.',
                'items' => [
                    ['display_type' => 'text', 'icon' => 'support_agent', 'logo_path' => '', 'logo_url' => '', 'name' => 'UII Dalwa'],
                    ['display_type' => 'text', 'icon' => 'history_edu', 'logo_path' => '', 'logo_url' => '', 'name' => 'Bahasa Arab'],
                    ['display_type' => 'icon', 'icon' => 'history_edu', 'logo_path' => '', 'logo_url' => '', 'name' => 'Mahasiswa'],
                    ['display_type' => 'icon', 'icon' => 'science', 'logo_path' => '', 'logo_url' => '', 'name' => 'Riset Prodi'],
                    ['display_type' => 'icon', 'icon' => 'history_edu', 'logo_path' => '', 'logo_url' => '', 'name' => 'Kajian Turats'],
                    ['display_type' => 'icon', 'icon' => 'public', 'logo_path' => '', 'logo_url' => '', 'name' => 'Budaya Arab'],
                    ['display_type' => 'icon', 'icon' => 'school', 'logo_path' => '', 'logo_url' => '', 'name' => 'Youtube'],
                ],
            ],
            'news' => [
                'kicker' => 'Sastra Arab Update',
                'title' => 'Berita, kegiatan, dan informasi prodi.',
                'description' => 'Kanal update dipakai untuk mengabarkan agenda akademik, kegiatan mahasiswa, informasi layanan, dan pengumuman prodi.',
                'button_label' => 'Semua News',
                'item_count' => 6,
                'fallback_items' => [
                    ['day' => '12', 'month' => 'Mei', 'category' => 'Akademik', 'title' => 'Agenda perkuliahan dan kegiatan Prodi Sastra Arab semester ini', 'body' => 'Informasi akademik terbaru disiapkan untuk mahasiswa, dosen, dan calon mahasiswa.', 'image' => '/img/news/news1.jpg'],
                    ['day' => '08', 'month' => 'Mei', 'category' => 'Mahasiswa', 'title' => 'Kegiatan diskusi sastra dan bahasa Arab dibuka untuk mahasiswa', 'body' => 'Program ini menjadi ruang penguatan wawasan kebahasaan, sastra, dan budaya Arab.', 'image' => '/img/news/news2.jpg'],
                    ['day' => '01', 'month' => 'Mei', 'category' => 'Prodi', 'title' => 'Profil Prodi Sastra Arab diperbarui untuk akses publik', 'body' => 'Website ini menyajikan informasi prodi, berita, layanan, dan kontak resmi.', 'image' => '/img/news/news3.jpg'],
                ],
            ],
            'about' => [
                'kicker' => 'Tentang Kami',
                'title' => 'Mengembangkan kajian bahasa, sastra, dan budaya Arab.',
                'body' => 'Program Studi Sastra Arab berfokus pada penguasaan bahasa Arab, analisis sastra, pemahaman budaya, dan penguatan tradisi keilmuan. Website ini menjadi kanal informasi akademik, berita, kegiatan, dan layanan prodi.',
                'stats' => [
                    ['value' => '4', 'label' => 'Fokus Kajian'],
                    ['value' => '30+', 'label' => 'Kegiatan Akademik'],
                    ['value' => '100+', 'label' => 'Mahasiswa Aktif'],
                ],
            ],
            'publishCta' => [
                'kicker' => 'Sastra Arab',
                'title' => 'Temukan informasi akademik dan kegiatan terbaru Program Studi Sastra Arab.',
                'button_label' => 'Hubungi Prodi',
                'button_href' => '#contact',
            ],
            'strengths' => [
                'kicker' => 'Kenapa Sastra Arab?',
                'title' => 'Ruang akademik untuk bahasa, sastra, budaya, dan tradisi keilmuan Arab.',
                'items' => [
                    ['icon' => 'history_edu', 'title' => 'Kajian Sastra', 'body' => 'Mahasiswa diarahkan memahami karya sastra Arab klasik, modern, dan kontemporer.'],
                    ['icon' => 'record_voice_over', 'title' => 'Kompetensi Bahasa', 'body' => 'Pembelajaran menekankan kemampuan membaca, menulis, berbicara, dan menganalisis teks Arab.'],
                    ['icon' => 'history_edu', 'title' => 'Tradisi Keilmuan', 'body' => 'Kajian prodi terhubung dengan turats, budaya Arab, dan khazanah keilmuan Islam.'],
                    ['icon' => 'forum', 'title' => 'Diskusi Akademik', 'body' => 'Kegiatan kelas dan forum ilmiah mendorong mahasiswa berpikir kritis dan komunikatif.'],
                    ['icon' => 'public', 'title' => 'Wawasan Budaya', 'body' => 'Mahasiswa mempelajari konteks budaya, sejarah, dan pemikiran Arab secara luas.'],
                    ['icon' => 'handshake', 'title' => 'Kolaborasi', 'body' => 'Prodi terbuka untuk kerja sama akademik, seminar, dan pengembangan kompetensi mahasiswa.'],
                ],
            ],
            'flow' => [
                'kicker' => 'Kegiatan Prodi',
                'title' => 'Pengalaman belajar yang terarah dan aktif.',
                'items' => [
                    ['no' => '01', 'title' => 'Pembelajaran', 'body' => 'Mahasiswa mengikuti perkuliahan inti bahasa, sastra, dan budaya Arab.'],
                    ['no' => '02', 'title' => 'Diskusi Ilmiah', 'body' => 'Forum akademik memperkuat analisis teks, riset, dan komunikasi.'],
                    ['no' => '03', 'title' => 'Praktik Bahasa', 'body' => 'Kegiatan praktik membantu mahasiswa memakai bahasa Arab secara aktif.'],
                    ['no' => '04', 'title' => 'Riset', 'body' => 'Mahasiswa diarahkan menyusun kajian dan karya ilmiah berbasis bidang prodi.'],
                    ['no' => '05', 'title' => 'Pengabdian', 'body' => 'Kompetensi diterapkan dalam kegiatan masyarakat dan jejaring akademik.'],
                ],
            ],
            'testimonials' => [
                'kicker' => 'Testimoni Klien',
                'title' => 'Cerita penulis yang tumbuh bersama press.',
                'items' => [
                    ['quote' => 'Pembelajaran Sastra Arab membuka cara baru memahami bahasa, teks, dan budaya secara mendalam.', 'name' => 'Dr. Ahmad Fikri', 'role' => 'Dosen'],
                    ['quote' => 'Kegiatan diskusi dan praktik bahasa membantu mahasiswa lebih percaya diri menggunakan bahasa Arab.', 'name' => 'Ust. Zainal Abidin', 'role' => 'Pengajar'],
                    ['quote' => 'Website prodi memudahkan akses informasi akademik, berita, dan layanan komunikasi.', 'name' => 'Laila Rahmah', 'role' => 'Mahasiswa'],
                ],
            ],
            'contact' => [
                'kicker' => 'Contact',
                'title' => 'Hubungi Program Studi Sastra Arab.',
                'body' => 'Sampaikan kebutuhan informasi akademik, kegiatan mahasiswa, kerja sama, dan layanan komunikasi prodi.',
                'email' => 'sastraarab@uiidalwa.ac.id',
                'phone' => '+62 812 0000 0000',
                'address' => 'Kompleks UII Dalwa, Pasuruan',
                'login_label' => 'Admin Login',
            ],
            'footer' => [
                'brand_title' => 'Sastra Arab',
                'brand_subtitle' => 'Program Studi',
                'description' => 'Website Program Studi Sastra Arab untuk informasi akademik, berita, kegiatan, dan layanan komunikasi prodi.',
                'primary_label' => 'Lihat News',
                'primary_href' => '/news',
                'secondary_label' => 'Kontak Prodi',
                'secondary_href' => '/contact',
                'navigation_title' => 'Navigasi',
                'navigation' => [
                    ['label' => 'Home', 'href' => '/'],
                    ['label' => 'Profil', 'href' => '/#about'],
                    ['label' => 'Akademik', 'href' => '/#services'],
                    ['label' => 'News', 'href' => '/news'],
                    ['label' => 'Contact', 'href' => '/contact'],
                ],
                'services_title' => 'Layanan',
                'services' => [
                    ['label' => 'Informasi Akademik', 'href' => '/#services'],
                    ['label' => 'Kegiatan Mahasiswa', 'href' => '/#flow'],
                    ['label' => 'Berita Prodi', 'href' => '/news'],
                    ['label' => 'Kontak Prodi', 'href' => '/contact'],
                ],
                'contact_title' => 'Kontak',
                'email' => 'sastraarab@uiidalwa.ac.id',
                'phone' => '082229872328',
                'whatsapp' => '082229872328',
                'address' => 'Pasuruan, Jawa Timur',
                'maps_href' => 'https://maps.app.goo.gl/pNNU4dnCtuu7y4Qk6',
                'copyright' => 'Sastra Arab. All rights reserved.',
                'admin_label' => 'Admin Login',
                'admin_href' => '/login',
                'maps_label' => 'Maps',
            ],
        ];
    }
}

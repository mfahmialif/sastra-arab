<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function general()
    {
        return response()->json($this->generalPayload());
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'system_name' => 'required|string|max:120',
            'accent_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,svg,webp|max:1024',
            'navbar_brand_mode' => ['nullable', Rule::in(['text', 'logo'])],
            'navbar_text_icon' => 'nullable|string|max:80',
            'navbar_text_title' => 'nullable|string|max:80',
            'navbar_text_subtitle' => 'nullable|string|max:80',
            'navbar_logo' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        AppSetting::setValue('system_name', $data['system_name']);
        AppSetting::setValue('accent_color', strtolower($data['accent_color']));
        AppSetting::setValue('navbar_brand_mode', $data['navbar_brand_mode'] ?? 'text');
        AppSetting::setValue('navbar_text_icon', $data['navbar_text_icon'] ?: 'auto_stories');
        AppSetting::setValue('navbar_text_title', $data['navbar_text_title'] ?: $data['system_name']);
        AppSetting::setValue('navbar_text_subtitle', $data['navbar_text_subtitle'] ?: 'Program Studi');

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('settings', 'public');
            AppSetting::setValue('favicon_path', $path);
            AppSetting::setValue('favicon_url', Storage::disk('public')->url($path));
        }

        if ($request->hasFile('navbar_logo')) {
            $path = $request->file('navbar_logo')->store('settings', 'public');
            AppSetting::setValue('navbar_logo_path', $path);
            AppSetting::setValue('navbar_logo_url', Storage::disk('public')->url($path));
        }

        return response()->json($this->generalPayload());
    }

    public function landing()
    {
        return response()->json($this->landingPayload());
    }

    public function login()
    {
        return response()->json($this->loginPayload());
    }

    public function updateLogin(Request $request)
    {
        $data = $request->validate([
            'login_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
        ]);

        if ($request->hasFile('login_image')) {
            $path = $request->file('login_image')->store('settings/login', 'public');
            AppSetting::setValue('login_image_path', $path);
            AppSetting::setValue('login_image_url', Storage::disk('public')->url($path));
        }

        return response()->json($this->loginPayload());
    }

    public function updateLanding(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'hero_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'supporter_logos' => 'nullable|array',
            'supporter_logos.*' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:2048',
        ]);

        $content = json_decode($data['content'], true);

        if (! is_array($content)) {
            return response()->json([
                'message' => 'Format pengaturan landing tidak valid.',
                'errors' => ['content' => ['Format pengaturan landing tidak valid.']],
            ], 422);
        }

        $current = $this->landingContent();
        $content = array_replace_recursive($current, $content);

        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('settings', 'public');
            $content['hero']['image_path'] = $path;
            $content['hero']['image_url'] = Storage::disk('public')->url($path);
        }

        foreach ($request->file('supporter_logos', []) as $index => $file) {
            if (! isset($content['supporters']['items'][$index])) {
                continue;
            }

            $path = $file->store('settings/supporters', 'public');
            $content['supporters']['items'][$index]['display_type'] = 'logo';
            $content['supporters']['items'][$index]['logo_path'] = $path;
            $content['supporters']['items'][$index]['logo_url'] = Storage::disk('public')->url($path);
        }

        AppSetting::setValue('landing_content', json_encode($content, JSON_UNESCAPED_UNICODE));

        return response()->json($this->landingPayload());
    }

    public function email()
    {
        return response()->json([
            'smtp_enabled' => AppSetting::getValue('smtp_enabled', false),
            'smtp_host' => AppSetting::getValue('smtp_host', ''),
            'smtp_port' => AppSetting::getValue('smtp_port', 587),
            'smtp_username' => AppSetting::getValue('smtp_username', ''),
            'smtp_password' => AppSetting::getValue('smtp_password', ''),
            'smtp_encryption' => AppSetting::getValue('smtp_encryption', 'tls'),
            'smtp_from_address' => AppSetting::getValue('smtp_from_address', ''),
            'smtp_from_name' => AppSetting::getValue('smtp_from_name', AppSetting::getValue('system_name', 'Sastra Arab')),
            'email_registration_default_role_id' => AppSetting::getValue('email_registration_default_role_id'),
            'roles' => $this->dashboardRoles(),
        ]);
    }

    public function updateEmail(Request $request)
    {
        $data = $request->validate([
            'smtp_enabled' => 'required|boolean',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:500',
            'smtp_encryption' => ['nullable', Rule::in(['', 'tls', 'ssl'])],
            'smtp_from_address' => 'required|email|max:255',
            'smtp_from_name' => 'required|string|max:255',
            'email_registration_default_role_id' => [
                'nullable',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->where('status', 'Active')),
            ],
        ]);

        AppSetting::setValue('smtp_enabled', $data['smtp_enabled'], 'boolean');
        AppSetting::setValue('smtp_host', $data['smtp_host'] ?? '');
        AppSetting::setValue('smtp_port', $data['smtp_port'], 'integer');
        AppSetting::setValue('smtp_username', $data['smtp_username'] ?? '');
        AppSetting::setValue('smtp_password', $data['smtp_password'] ?? '');
        AppSetting::setValue('smtp_encryption', $data['smtp_encryption'] ?? '');
        AppSetting::setValue('smtp_from_address', $data['smtp_from_address']);
        AppSetting::setValue('smtp_from_name', $data['smtp_from_name']);
        AppSetting::setValue('email_registration_default_role_id', $data['email_registration_default_role_id'] ?? null, 'integer');

        return $this->email();
    }

    public function googleLogin()
    {
        return response()->json([
            'google_login_enabled' => AppSetting::getValue('google_login_enabled', false),
            'google_client_id' => AppSetting::getValue('google_client_id', ''),
            'google_client_secret' => AppSetting::getValue('google_client_secret', ''),
            'google_hosted_domain' => AppSetting::getValue('google_hosted_domain', ''),
            'google_auto_create_users' => AppSetting::getValue('google_auto_create_users', false),
            'google_default_role_id' => AppSetting::getValue('google_default_role_id'),
            'roles' => Role::query()
                ->where('status', 'Active')
                ->whereIn('name', ['Admin', 'Operator', 'Penulis', 'Kepala Penulis'])
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function updateGoogleLogin(Request $request)
    {
        $data = $request->validate([
            'google_login_enabled' => 'required|boolean',
            'google_client_id' => 'nullable|string|max:500',
            'google_client_secret' => 'nullable|string|max:500',
            'google_hosted_domain' => 'nullable|string|max:255',
            'google_auto_create_users' => 'required|boolean',
            'google_default_role_id' => [
                'nullable',
                Rule::exists('roles', 'id')->where(fn ($query) => $query->where('status', 'Active')),
            ],
        ]);

        AppSetting::setValue('google_login_enabled', $data['google_login_enabled'], 'boolean');
        AppSetting::setValue('google_client_id', $data['google_client_id'] ?? '');
        AppSetting::setValue('google_client_secret', $data['google_client_secret'] ?? '');
        AppSetting::setValue('google_hosted_domain', $data['google_hosted_domain'] ?? '');
        AppSetting::setValue('google_auto_create_users', $data['google_auto_create_users'], 'boolean');
        AppSetting::setValue('google_default_role_id', $data['google_default_role_id'] ?? null, 'integer');

        return $this->googleLogin();
    }

    private function generalPayload(): array
    {
        return [
            'system_name' => AppSetting::getValue('system_name', 'Sastra Arab'),
            'accent_color' => AppSetting::getValue('accent_color', '#2563eb'),
            'favicon_path' => AppSetting::getValue('favicon_path', ''),
            'favicon_url' => AppSetting::getValue('favicon_url', ''),
            'navbar_brand_mode' => AppSetting::getValue('navbar_brand_mode', 'text'),
            'navbar_text_icon' => AppSetting::getValue('navbar_text_icon', 'auto_stories'),
            'navbar_text_title' => AppSetting::getValue('navbar_text_title', AppSetting::getValue('system_name', 'Sastra Arab')),
            'navbar_text_subtitle' => AppSetting::getValue('navbar_text_subtitle', 'Program Studi'),
            'navbar_logo_path' => AppSetting::getValue('navbar_logo_path', ''),
            'navbar_logo_url' => AppSetting::getValue('navbar_logo_url', ''),
        ];
    }

    private function landingPayload(): array
    {
        return $this->landingContent();
    }

    private function loginPayload(): array
    {
        return [
            'image_path' => AppSetting::getValue('login_image_path', ''),
            'image_url' => AppSetting::getValue('login_image_url', ''),
        ];
    }

    private function landingContent(): array
    {
        $stored = AppSetting::getValue('landing_content');
        $content = is_string($stored) ? json_decode($stored, true) : null;
        $hasStoredContent = is_array($content);
        $content = is_array($content) ? $content : [];

        $legacyHero = $hasStoredContent ? [] : [
            'image_path' => AppSetting::getValue('hero_image_path', ''),
            'image_url' => AppSetting::getValue('hero_image_url', ''),
            'image_alt' => AppSetting::getValue('hero_image_alt', 'Kegiatan Program Studi Sastra Arab'),
            'label' => AppSetting::getValue('hero_label', ''),
            'title' => AppSetting::getValue('hero_title', ''),
        ];

        return array_replace_recursive($this->defaultLandingContent(), $content, [
            'hero' => array_filter($legacyHero, fn ($value) => $value !== null && $value !== ''),
        ]);
    }

    private function defaultLandingContent(): array
    {
        return [
            'hero' => [
                'image_path' => '',
                'image_url' => '',
                'image_alt' => 'Kegiatan Program Studi Sastra Arab',
                'eyebrow' => 'PROGRAM STUDI',
                'main_title' => 'Sastra Arab',
                'description' => 'Website resmi Program Studi Sastra Arab untuk informasi akademik, berita, kegiatan mahasiswa, dan layanan komunikasi prodi.',
                'primary_label' => 'Lihat Akademik',
                'primary_href' => '#services',
                'secondary_label' => 'Kegiatan Prodi',
                'secondary_href' => '#flow',
                'label' => '',
                'title' => '',
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
                    ['icon' => 'school', 'title' => 'Informasi Akademik', 'body' => 'Akses informasi kurikulum, kegiatan kuliah, dan agenda akademik prodi.', 'href' => '#services'],
                    ['icon' => 'translate', 'title' => 'Kajian Bahasa Arab', 'body' => 'Pembelajaran bahasa, sastra, linguistik, dan tradisi keilmuan Arab.', 'href' => '#about'],
                    ['icon' => 'groups', 'title' => 'Kegiatan Mahasiswa', 'body' => 'Ikuti kabar seminar, diskusi ilmiah, komunitas, dan aktivitas kemahasiswaan.', 'href' => '#flow'],
                    ['icon' => 'support_agent', 'title' => 'Layanan Prodi', 'body' => 'Hubungi pengelola prodi untuk kebutuhan administrasi dan informasi studi.', 'href' => '#contact'],
                ],
            ],
            'supporters' => [
                'kicker' => 'Supporter',
                'title' => 'Ekosistem yang mendukung gerakan literasi kampus.',
                'items' => [
                    ['display_type' => 'icon', 'icon' => 'school', 'logo_path' => '', 'logo_url' => '', 'name' => 'UII Dalwa'],
                    ['display_type' => 'icon', 'icon' => 'translate', 'logo_path' => '', 'logo_url' => '', 'name' => 'Bahasa Arab'],
                    ['display_type' => 'icon', 'icon' => 'diversity_3', 'logo_path' => '', 'logo_url' => '', 'name' => 'Mahasiswa'],
                    ['display_type' => 'icon', 'icon' => 'science', 'logo_path' => '', 'logo_url' => '', 'name' => 'Riset Prodi'],
                    ['display_type' => 'icon', 'icon' => 'history_edu', 'logo_path' => '', 'logo_url' => '', 'name' => 'Kajian Turats'],
                    ['display_type' => 'icon', 'icon' => 'public', 'logo_path' => '', 'logo_url' => '', 'name' => 'Budaya Arab'],
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
                'phone' => '+62 812 0000 0000',
                'whatsapp' => '6281200000000',
                'address' => 'Pasuruan, Jawa Timur',
                'maps_href' => 'https://maps.app.goo.gl/pNNU4dnCtuu7y4Qk6',
                'copyright' => 'Sastra Arab. All rights reserved.',
                'admin_label' => 'Admin Login',
                'admin_href' => '/login',
                'maps_label' => 'Maps',
            ],
        ];
    }

    private function dashboardRoles()
    {
        return Role::query()
            ->where('status', 'Active')
            ->whereIn('name', ['Admin', 'Operator', 'Penulis', 'Kepala Penulis'])
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}

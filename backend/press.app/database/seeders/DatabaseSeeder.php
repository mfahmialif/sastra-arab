<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = Role::updateOrCreate(['name' => 'Admin'], [
            'description' => 'Full access untuk mengelola website Sastra Arab.',
            'status' => 'Active',
        ]);

        $operator = Role::updateOrCreate(['name' => 'Operator'], [
            'description' => 'Dapat mengelola dan menerbitkan konten website.',
            'status' => 'Active',
        ]);

        $penulisRole = Role::updateOrCreate(['name' => 'Penulis'], [
            'description' => 'Akses dashboard penulis untuk mengelola artikel berita miliknya sendiri.',
            'status' => 'Active',
        ]);

        $kepalaPenulisRole = Role::updateOrCreate(['name' => 'Kepala Penulis'], [
            'description' => 'Akses dashboard kepala penulis untuk mengelola artikel dan akun penulis.',
            'status' => 'Active',
        ]);

        $adminUser = User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Administrator',
            'email' => 'admin@sastraarab.local',
            'password' => bcrypt('password'),
            'role_id' => $admin->id,
            'status' => 'Active',
            'last_active_at' => now(),
        ]);

        User::updateOrCreate(['username' => 'operator'], [
            'name' => 'Operator Konten',
            'email' => 'operator@sastraarab.local',
            'password' => bcrypt('password'),
            'role_id' => $operator->id,
            'status' => 'Active',
            'last_active_at' => now()->subHours(2),
        ]);

        $penulisUser = User::updateOrCreate(['username' => 'penulis'], [
            'name' => 'Penulis Berita',
            'email' => 'penulis@sastraarab.local',
            'password' => bcrypt('password'),
            'role_id' => $penulisRole->id,
            'status' => 'Active',
            'last_active_at' => now()->subMinutes(25),
        ]);

        User::updateOrCreate(['username' => 'kepala_penulis'], [
            'name' => 'Kepala Penulis',
            'email' => 'kepala.penulis@sastraarab.local',
            'password' => bcrypt('password'),
            'role_id' => $kepalaPenulisRole->id,
            'status' => 'Active',
            'last_active_at' => now()->subMinutes(20),
        ]);

        AppSetting::setValue('system_name', 'Sastra Arab');
        AppSetting::setValue('smtp_from_name', 'Sastra Arab');

        $categories = collect([
            [
                'name' => 'Akademik',
                'slug' => 'akademik',
                'type' => 'Artikel',
                'description' => 'Informasi akademik Program Studi Sastra Arab.',
            ],
            [
                'name' => 'Kegiatan Mahasiswa',
                'slug' => 'kegiatan-mahasiswa',
                'type' => 'Artikel',
                'description' => 'Kabar kegiatan mahasiswa dan komunitas akademik Sastra Arab.',
            ],
            [
                'name' => 'Galeri Prodi',
                'slug' => 'galeri-prodi',
                'type' => 'Gambar',
                'description' => 'Dokumentasi visual kegiatan Program Studi Sastra Arab.',
            ],
            [
                'name' => 'Video Prodi',
                'slug' => 'video-prodi',
                'type' => 'Video',
                'description' => 'Konten video kegiatan dan informasi Program Studi Sastra Arab.',
            ],
        ])->mapWithKeys(fn ($category) => [
            $category['slug'] => NewsCategory::updateOrCreate(['slug' => $category['slug']], $category),
        ]);

        $newsItems = [
            [
                'title' => 'Website Program Studi Sastra Arab Resmi Diperbarui',
                'category_slug' => 'akademik',
                'body' => '<p>Website Sastra Arab hadir sebagai kanal informasi akademik, berita, kegiatan, dan layanan komunikasi prodi.</p>',
                'status' => 'Published',
            ],
            [
                'title' => 'Diskusi Bahasa dan Sastra Arab untuk Mahasiswa',
                'category_slug' => 'kegiatan-mahasiswa',
                'body' => '<p>Kegiatan diskusi rutin menjadi ruang penguatan kompetensi bahasa, analisis teks, dan wawasan budaya Arab.</p>',
                'status' => 'Published',
            ],
            [
                'title' => 'Dokumentasi Kegiatan Akademik Sastra Arab',
                'category_slug' => 'galeri-prodi',
                'body' => null,
                'status' => 'Published',
            ],
            [
                'title' => 'Profil Program Studi Sastra Arab',
                'category_slug' => 'video-prodi',
                'body' => '<p>Video pengenalan prodi memuat informasi singkat tentang pembelajaran, kegiatan, dan layanan Sastra Arab.</p>',
                'speaker' => 'Tim Program Studi Sastra Arab',
                'duration' => 1800,
                'status' => 'Draft',
            ],
        ];

        foreach ($newsItems as $index => $item) {
            $categorySlug = $item['category_slug'];
            unset($item['category_slug']);

            $news = News::updateOrCreate(['title' => $item['title']], [
                ...$item,
                'news_category_id' => $categories[$categorySlug]->id,
                'created_by' => $adminUser->id,
                'author_id' => $index === 1 ? $penulisUser->id : null,
                'created_at' => now()->subHours($index * 3),
                'updated_at' => now()->subHours($index * 3),
            ]);
            $news->categories()->syncWithoutDetaching([$categories[$categorySlug]->id]);
        }

        $this->command?->newLine();
        $this->command?->info('Login seed accounts:');
        $this->command?->line('Admin         : username=admin          password=password');
        $this->command?->line('Operator      : username=operator       password=password');
        $this->command?->line('Penulis       : username=penulis        password=password');
        $this->command?->line('Kepala Penulis: username=kepala_penulis password=password');
        $this->command?->newLine();
    }
}

<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::query()->where('username', 'admin')->first();
        $penulisUser = User::query()->where('username', 'penulis')->first();

        if (! $adminUser) {
            $this->call(UserSeeder::class);
            $adminUser = User::query()->where('username', 'admin')->first();
            $penulisUser = User::query()->where('username', 'penulis')->first();
        }

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
                'slug' => 'website-program-studi-sastra-arab-resmi-diperbarui',
                'category_slug' => 'akademik',
                'body' => '<p>Website Sastra Arab hadir sebagai kanal informasi akademik, berita, kegiatan, dan layanan komunikasi prodi.</p>',
                'status' => 'Published',
            ],
            [
                'title' => 'Diskusi Bahasa dan Sastra Arab untuk Mahasiswa',
                'slug' => 'diskusi-bahasa-dan-sastra-arab-untuk-mahasiswa',
                'category_slug' => 'kegiatan-mahasiswa',
                'body' => '<p>Kegiatan diskusi rutin menjadi ruang penguatan kompetensi bahasa, analisis teks, dan wawasan budaya Arab.</p>',
                'status' => 'Published',
            ],
            [
                'title' => 'Dokumentasi Kegiatan Akademik Sastra Arab',
                'slug' => 'dokumentasi-kegiatan-akademik-sastra-arab',
                'category_slug' => 'galeri-prodi',
                'body' => null,
                'status' => 'Published',
            ],
            [
                'title' => 'Profil Program Studi Sastra Arab',
                'slug' => 'profil-program-studi-sastra-arab',
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

            $payload = [
                ...$item,
                'news_category_id' => $categories[$categorySlug]->id,
                'created_by' => $adminUser?->id,
                'author_id' => $index === 1 ? $penulisUser?->id : null,
                'created_at' => now()->subHours($index * 3),
                'updated_at' => now()->subHours($index * 3),
            ];

            $news = News::query()
                ->where('slug', $item['slug'])
                ->orWhere('title', $item['title'])
                ->first();

            if ($news) {
                $news->fill($payload);
                $news->save();
            } else {
                $news = News::create($payload);
            }

            $news->categories()->syncWithoutDetaching([$categories[$categorySlug]->id]);
        }
    }
}

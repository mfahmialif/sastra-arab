<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class SpaSeoController extends Controller
{
    public function __invoke(Request $request, ?string $path = null)
    {
        $meta = $this->metaForPath('/' . ltrim($path ?? '', '/'), $request);
        $html = $this->spaHtml();

        if ($html === null) {
            return view('welcome');
        }

        return response($this->injectMeta($html, $meta), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    private function metaForPath(string $path, Request $request): array
    {
        $siteName = AppSetting::getValue('system_name', 'Sastra Arab');
        $landing = $this->landingContent();
        $defaultDescription = $landing['hero']['description'] ?? 'Website resmi Program Studi Sastra Arab untuk informasi akademik, berita, kegiatan mahasiswa, dan layanan komunikasi prodi.';
        $defaultImage = $this->absoluteUrl($landing['hero']['image_url'] ?? '') ?: $this->absoluteUrl('/img/news/news1.jpg');
        $canonical = URL::to($request->getRequestUri());

        $meta = [
            'title' => $siteName,
            'description' => $defaultDescription,
            'image' => $defaultImage,
            'url' => $canonical,
            'type' => 'website',
            'site_name' => $siteName,
        ];

        if ($path === '/' || $path === '') {
            return $meta;
        }

        if ($path === '/news') {
            return array_replace($meta, [
                'title' => 'News - ' . $siteName,
                'description' => $landing['news']['description'] ?? 'Berita, kegiatan, dan informasi terbaru Program Studi Sastra Arab.',
            ]);
        }

        if (preg_match('#^/news/([^/]+)$#', $path, $matches)) {
            $news = News::findBySlugOrId(urldecode($matches[1]));
            if ($news) {
                return array_replace($meta, [
                    'title' => $news->title . ' - ' . $siteName,
                    'description' => $this->excerpt($news->body) ?: ($news->title . ' - ' . $siteName),
                    'image' => $this->newsImage($news) ?: $defaultImage,
                    'type' => 'article',
                ]);
            }
        }

        if (preg_match('#^/pages/([^/]+)$#', $path, $matches)) {
            $page = Page::query()
                ->where('slug', urldecode($matches[1]))
                ->where('status', 'Published')
                ->first();

            if ($page) {
                return array_replace($meta, [
                    'title' => $page->title . ' - ' . $siteName,
                    'description' => $this->excerpt($page->body) ?: ($page->title . ' - ' . $siteName),
                    'image' => $this->firstImageFromHtml($page->body) ?: $defaultImage,
                    'type' => 'article',
                ]);
            }
        }

        return $meta;
    }

    private function spaHtml(): ?string
    {
        $paths = [
            public_path('index.html'),
            base_path('../../frontend/dist/index.html'),
        ];

        foreach ($paths as $path) {
            if ($path && File::exists($path)) {
                return File::get($path);
            }
        }

        return null;
    }

    private function injectMeta(string $html, array $meta): string
    {
        $tags = implode("\n    ", [
            '<title>' . e($meta['title']) . '</title>',
            '<meta name="description" content="' . e($meta['description']) . '">',
            '<link rel="canonical" href="' . e($meta['url']) . '">',
            '<meta property="og:locale" content="id_ID">',
            '<meta property="og:type" content="' . e($meta['type']) . '">',
            '<meta property="og:site_name" content="' . e($meta['site_name']) . '">',
            '<meta property="og:title" content="' . e($meta['title']) . '">',
            '<meta property="og:description" content="' . e($meta['description']) . '">',
            '<meta property="og:url" content="' . e($meta['url']) . '">',
            '<meta property="og:image" content="' . e($meta['image']) . '">',
            '<meta property="og:image:secure_url" content="' . e($meta['image']) . '">',
            '<meta name="twitter:card" content="summary_large_image">',
            '<meta name="twitter:title" content="' . e($meta['title']) . '">',
            '<meta name="twitter:description" content="' . e($meta['description']) . '">',
            '<meta name="twitter:image" content="' . e($meta['image']) . '">',
        ]);

        $html = preg_replace('/<title>.*?<\/title>/is', '', $html, 1);
        $html = preg_replace('/<meta\s+name=["\']description["\'][^>]*>/i', '', $html, 1);
        $html = preg_replace('/<link\s+rel=["\']canonical["\'][^>]*>/i', '', $html);
        $html = preg_replace('/<meta\s+property=["\']og:[^"\']+["\'][^>]*>/i', '', $html);
        $html = preg_replace('/<meta\s+name=["\']twitter:[^"\']+["\'][^>]*>/i', '', $html);

        return str_replace('</head>', "    {$tags}\n  </head>", $html);
    }

    private function landingContent(): array
    {
        $stored = AppSetting::getValue('landing_content');
        $content = is_string($stored) ? json_decode($stored, true) : null;

        return is_array($content) ? $content : [];
    }

    private function newsImage(News $news): ?string
    {
        if (! $news->image_path) {
            return null;
        }

        return $this->absoluteUrl('/storage/' . ltrim($news->image_path, '/'));
    }

    private function firstImageFromHtml(?string $html): ?string
    {
        if (! $html || ! preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches)) {
            return null;
        }

        return $this->absoluteUrl($matches[1]);
    }

    private function absoluteUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        return URL::to('/' . ltrim($url, '/'));
    }

    private function excerpt(?string $html): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $html)));

        return mb_strlen($text) > 160 ? mb_substr($text, 0, 157) . '...' : $text;
    }
}

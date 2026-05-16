# Rencana Implementasi CMS Pages dan Manajemen Navbar

## Summary
Implementasikan pola mirip WordPress: berita yang sudah ada diperlakukan sebagai **Posts/Berita**, tambah modul **Pages** untuk halaman statis, dan tambah **Manajemen Menu** agar navbar publik bisa diatur bebas dari admin. Menu mendukung item dari Pages, Posts, dan Custom URL, dengan struktur multi-level.

## Key Changes
- Backend Laravel:
  - Tambah model, migration, controller, dan API untuk `pages`.
  - `pages` memiliki minimal: `title`, `slug`, `body`, `status` Published/Draft, `created_by`, timestamps.
  - Tambah slug ke `news` agar post bisa diakses via `/news/{slug}`; route lama `/news/{id}` tetap kompatibel.
  - Tambah model dan API untuk menu: `menus` dan `menu_items`.
  - Gunakan satu menu utama default: `primary`.
  - `menu_items` menyimpan `label`, `type` (`page`, `post`, `custom`), `reference_id`, `url`, `parent_id`, `sort_order`, `is_active`, dan `target`.
  - Public API baru:
    - `GET /navigation/primary`
    - `GET /pages/{slug}`
    - `GET /news/{slugOrId}`
  - Admin API baru:
    - CRUD `/pages`
    - CRUD/reorder `/menus/primary/items`
    - endpoint options untuk memilih Pages dan Posts saat membuat menu.

- Frontend Vue:
  - Tambah admin menu sidebar/horizontal: **Pages** dan **Manajemen Menu** di grup Website/CMS.
  - Tambah halaman admin Pages: list, create, edit, delete, status Published/Draft, editor rich text memakai Quill seperti form News.
  - Tambah halaman admin Manajemen Menu:
    - bisa tambah item dari Page, Post, atau Custom URL.
    - bisa edit label, status aktif, target tab, parent, dan urutan.
    - mendukung multi-level secara data; UI minimal pakai daftar bertingkat dengan kontrol parent dan tombol naik/turun.
  - Navbar publik mengambil data dari `GET /navigation/primary`, bukan lagi array statis di `PublicLayout.vue`.
  - Render menu multi-level sebagai dropdown desktop dan nested list di mobile.
  - Tambah public view untuk Pages di `/pages/:slug`.

- Default data:
  - Seeder membuat menu `primary` berisi menu awal yang sama dengan sekarang:
    - Home `/`
    - Profil `/#about`
    - Akademik `/#services`
    - News `/news`
    - Contact `/contact`
  - Item awal tersebut memakai tipe `custom` agar anchor landing tetap bisa dipakai.

## Public Interfaces
- Pages publik:
  - `/pages/:slug`
- Posts publik:
  - `/news/:slugOrId`
- Menu publik:
  - `GET /api/navigation/primary` mengembalikan tree menu aktif yang sudah siap dirender:
    - `label`
    - `url`
    - `target`
    - `type`
    - `children`

## Test Plan
- Jalankan migration dan seeder.
- Backend:
  - test CRUD Pages, validasi slug unik, dan hanya Published yang bisa diakses publik.
  - test `GET /news/{slugOrId}` dengan slug baru dan ID lama.
  - test menu tree: item inactive tidak tampil, urutan benar, children masuk ke parent.
- Frontend:
  - build frontend dengan `npm run build`.
  - cek admin bisa membuat Page, membuat item menu Page/Post/Custom, mengubah parent dan urutan.
  - cek navbar desktop menampilkan dropdown multi-level.
  - cek navbar mobile menampilkan nested menu dan link menutup panel setelah diklik.
  - cek link custom seperti `/#about`, `/contact`, dan external URL tetap berfungsi.

## Assumptions
- Hak kelola Pages dan Menu hanya untuk role admin/operator yang sudah bisa masuk area `/administrator`.
- Existing `news` table tetap dipakai sebagai sumber Posts/Berita agar fitur berita lama tidak rusak.
- Permalink memakai pola aman: Pages di `/pages/slug`, Posts di `/news/slug`.
- Multi-level disimpan fleksibel di database, tetapi UI navbar akan dibuat tetap rapi dan dibatasi secara visual agar tidak merusak layout.

# Install.md

## Interactive Setup Rules

Sebelum memulai development remake codebase website program studi, AI wajib melakukan interactive prompt step-by-step kepada user.

AI harus:
- Menginspeksi / membaca nama folder project saat ini terlebih dahulu
- Menggunakan nama folder sebagai acuan otomatis:
  - frontend
  - backend
  - domain
  - database
  - nama website
- Menunggu jawaban user sebelum lanjut ke pertanyaan berikutnya
- Memberikan quick options
- Tetap mengizinkan custom input

---

# Flow Pertanyaan

## 0. Inspect Folder Project

AI wajib membaca nama folder project aktif terlebih dahulu.

Contoh:
- Folder: spi

Maka rekomendasi otomatis:
- Frontend production:
  - https://spi.uiidalwa.ac.id
- Backend production:
  - https://spiapp.uiidalwa.ac.id
- Database:
  - spi_app

---

# 1. URL Frontend Lokal

Tanyakan:

"Masukkan URL frontend lokal"

Pilihan cepat:
- http://localhost:3000
- http://localhost:5173
- http://127.0.0.1:3000

Tetap izinkan custom input.

---

# 2. URL Backend Lokal

Tanyakan:

"Masukkan URL backend lokal"

Gunakan nama folder project sebagai acuan.

Format rekomendasi:
- http://localhost/{namafolder}app/backend/public_html

Contoh:
- http://localhost/spiapp/backend/public_html
- http://localhost/paiapp/backend/public_html

Pilihan tambahan:
- http://localhost:8000
- http://localhost:8080
- http://127.0.0.1:8000

Tetap izinkan custom input.

---

# 3. URL Frontend Production

Tanyakan:

"Masukkan URL frontend production"

Gunakan nama folder sebagai acuan.

Contoh rekomendasi:
- https://spi.uiidalwa.ac.id
- https://pai.uiidalwa.ac.id
- https://mpi.uiidalwa.ac.id

Tetap izinkan custom input.

---

# 4. URL Backend Production

Tanyakan:

"Masukkan URL backend production"

Gunakan format:
{namafolder}app

Contoh:
- https://spiapp.uiidalwa.ac.id
- https://paiapp.uiidalwa.ac.id
- https://mpiapp.uiidalwa.ac.id

Pilihan tambahan:
- https://api.domain.ac.id
- https://backend.domain.ac.id

Tetap izinkan custom input.

---

# 5. Nama Website

Tanyakan:

"Masukkan nama website"

Gunakan nama folder sebagai referensi awal.

Contoh:
Jika folder `spi`
- Prodi SPI
- Website Prodi SPI
- Sistem Informasi SPI

Tetap izinkan custom input.

---

# 6. Nama Database

Tanyakan:

"Masukkan nama database"

Catatan:
Database harus sudah dibuat terlebih dahulu.

Gunakan nama folder sebagai acuan.

Contoh:
- spi_app
- pai_app
- mpi_app

Tetap izinkan custom input.

---

# 7. Warna Utama Website

Tanyakan:

"Pilih warna utama website"

Pilihan cepat:
- Biru
- Hijau
- Merah
- Emerald
- Navy
- Orange
- Ungu
- Custom

Jika memilih custom:
- Minta kode HEX atau nama warna.

---

# Setelah Semua Pertanyaan

Setelah seluruh jawaban diterima:

1. Tampilkan rangkuman konfigurasi
2. Buat file:
   - `_agent/specsweb.md`
3. Simpan seluruh hasil konfigurasi ke file tersebut
4. Gunakan `_agent/specsweb.md` sebagai acuan utama development
5. Jangan memulai development sebelum seluruh konfigurasi selesai dibuat

---

# Aturan Awal Implementasi Laravel

Sebelum memulai development backend Laravel:

## 1. Generate Environment File

Backend:
```bash
cp .env.example .env
```

Production:
```bash
cp .env.production.example .env.production
```

---

## 2. Update Seeder

Edit isi berikut menggunakan nama website:
- MenuPageSeeder
- NewsSeeder
- SettingSeeder

---

## 3. Generate Laravel Key

Jalankan:
```bash
php artisan key:generate
```

---

## 4. Jalankan Migration dan Seeder

Gunakan command yang benar:

```bash
php artisan migrate:fresh --seed
```

Bukan:
```bash
php artisan migrate:f --seed
```

---

## File yang perlu diedit: 

backend/press.app/.env
backend/press.app/.env.production
backend/press.app/app/Http/Controllers/SettingController.php
backend/press.app/database/seeders/MenuPageSeeder.php
backend/press.app/database/seeders/NewsSeeder.php
backend/press.app/database/seeders/SettingSeeder.php
frontend/.env.development
frontend/.env.production
frontend/index.html

---

# Catatan Penting

- AI tidak boleh langsung coding sebelum setup selesai
- AI wajib menunggu jawaban user setiap step
- Semua konfigurasi wajib terdokumentasi di:
  - `_agent/specsweb.md`
- Semua rekomendasi harus berbasis nama folder project
- Semua URL production harus konsisten dengan naming convention project

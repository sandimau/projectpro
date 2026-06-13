# Panduan Install untuk Perusahaan Baru

Project ini dipakai banyak perusahaan. Tiap perusahaan = 1 instalasi terpisah
(database & domain sendiri). Berikut yang **wajib diganti/diatur** per perusahaan.

## 1. File `.env`

Salin `.env.example` → `.env`, lalu sesuaikan:

```env
APP_NAME="Nama Perusahaan"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-perusahaan.com   # WAJIB benar — dipakai url() untuk redirect Shopee

DB_DATABASE=db_perusahaan
DB_USERNAME=...
DB_PASSWORD=...

SHOPEE_LIVE_PUSH_PARTNER_KEY=...         # hanya jika pakai Shopee Live Push
MAIL_*=...                               # email perusahaan
```

> ⚠️ `APP_URL` paling kritikal. `url('shopee/auth')` mengikuti nilai ini; jika
> salah, callback OAuth Shopee gagal dan token tidak tersimpan.

## 2. Setup aplikasi

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate
php artisan db:seed            # RoleSeeder + AdminSeeder + MarketplaceFormatSeeder
php artisan storage:link
```

> ⚠️ `AdminSeeder` membuat user `super@souvenirbag.net` / password `password`.
> **Ganti email & password** ini setelah login pertama (jangan biarkan default).

## 3. Kredensial Shopee Open Platform (tabel `marketplace_formats`)

Struktur format file (cara baca order/keuangan/stok) sudah di-seed otomatis.
Yang perlu diisi manual = kredensial Open Platform Shopee perusahaan tsb:

```sql
UPDATE marketplace_formats
SET partnerId  = <PARTNER_ID>,
    partnerKey = '<PARTNER_KEY>',
    host       = 'https://partner.shopeemobile.com/'   -- sandbox: https://partner.test-stable.shopeemobile.com/
WHERE marketplace = 'shopee' AND jenis = 'order';
```

## 4. Pengaturan di Console Shopee Open Platform (sisi Shopee)

Untuk app/akun Shopee perusahaan ini, daftarkan URL berikut (pakai HTTPS):

| Setting | Nilai |
|---|---|
| Redirect / Callback URL | `https://domain-perusahaan.com/shopee/auth` |
| Push / Webhook URL | `https://domain-perusahaan.com/webhook/shopee/push` |

## 5. Branding (tabel `sistems`)

Isi nama & logo perusahaan di tabel `sistems` (kolom `nama`, `isi`, `type`).
Logo tampil di header layout.

---

## Catatan penting

- Kredensial Shopee disimpan di **database (`marketplace_formats`), bukan `.env`**.
  Menyalin `.env` saja tidak cukup.
- Kolom `marketplaces.lock` dipakai untuk auto-refresh token. Migration
  `add_lock_to_marketplaces_table` menambalnya otomatis (termasuk DB lama).
- Setelah perusahaan menghubungkan toko: klik **"sinkronkan"** di halaman
  Marketplace → otorisasi di Shopee → token tersimpan otomatis.

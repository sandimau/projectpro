# Panduan Install Multi-Company (Subdomain)

Satu instalasi ProjectPro melayani banyak perusahaan. Setiap company diakses
via **subdomain**, mis. `souvenir.projectpro.com`, dan semua data diisolasi
oleh `company_id`.

## 1. File `.env`

Salin `.env.example` → `.env`, lalu sesuaikan:

```env
APP_NAME="ProjectPro"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://projectpro.com

# Domain pusat (tanpa subdomain). Pisahkan koma jika lebih dari satu.
CENTRAL_DOMAINS=projectpro.com,localhost

# Fallback company jika akses tanpa subdomain (disarankan untuk lokal path-based).
# Di production sebaiknya dikosongkan agar wajib pakai subdomain.
DEFAULT_COMPANY_SLUG=default

# Session: JANGAN set SESSION_DOMAIN=.projectpro.com
# (cookie harus per-host agar session tidak bocor antar company)
SESSION_DOMAIN=
SESSION_DRIVER=file

DB_DATABASE=projectpro
DB_USERNAME=...
DB_PASSWORD=...

MAIL_*=...
```

Pengaturan absensi (GPS/QR/WhatsApp) bisa tetap di `.env` sebagai default,
lalu di-copy ke `companies.settings` saat `company:create`. Setelah itu bisa
diubah per company di kolom JSON `settings`.

## 2. Setup aplikasi

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate
php artisan db:seed            # Permission + Role (+ Admin jika company default sudah ada dari migrasi)
php artisan storage:link
```

Migrasi akan:

1. Membuat tabel `companies`
2. Menambah `company_id` ke hampir semua tabel bisnis
3. Membuat company default (`DEFAULT_COMPANY_SLUG`, default: `default`) dan
   men-backfill data existing ke company itu

## 3. Buat company baru

```bash
php artisan company:create "Souvenir Bandung" souvenir \
  --email=admin@souvenir.com \
  --password='ganti-password'
```

Hasil:

- Baris di `companies` (slug = subdomain)
- User admin dengan role `super`
- Seed `sistems` + `marketplace_formats` untuk company itu

Akses: `https://souvenir.projectpro.com`

## 4. DNS & Web Server

**Production**

- Wildcard DNS: `*.projectpro.com` → IP server yang sama
- Nginx/Apache: server_name `projectpro.com` dan `*.projectpro.com`
- Document root tetap ke folder `public/`

**Lokal (WSL)**

- Tambah di `/etc/hosts` (contoh terbatas, wildcard lokal butuh dnsmasq):
  ```
  127.0.0.1 souvenir.localhost default.localhost
  ```
- Atau set `DEFAULT_COMPANY_SLUG=default` dan akses lewat `APP_URL` path-based
  seperti biasa selama development

## 5. Kredensial Shopee Open Platform

Per company, isi di tabel `marketplace_formats` (sudah ada `company_id`):

```sql
UPDATE marketplace_formats
SET partnerId  = <PARTNER_ID>,
    partnerKey = '<PARTNER_KEY>',
    host       = 'https://partner.shopeemobile.com/'
WHERE marketplace = 'shopee' AND jenis = 'order'
  AND company_id = <ID_COMPANY>;
```

Di Console Shopee, daftarkan URL **per subdomain company**:

| Setting | Nilai |
|---|---|
| Redirect / Callback URL | `https://souvenir.projectpro.com/shopee/auth` |
| Push / Webhook URL | `https://souvenir.projectpro.com/webhook/shopee/push` |

`APP_URL` di-force dinamis dari host request, jadi `url()` / OAuth mengikuti subdomain.

## 6. Cron multi-company

Jangan hit URL tanpa subdomain untuk job global. Pakai artisan:

```bash
php artisan company:cron buffer-proses
php artisan company:cron buffer-pending
php artisan company:cron buffer-wallet
php artisan company:cron sync-stok
php artisan company:cron refresh-token

# Hanya satu company:
php artisan company:cron buffer-proses --company=souvenir
```

Contoh crontab:

```cron
* * * * * cd /path/to/projectpro && php artisan company:cron buffer-proses >> /dev/null 2>&1
```

Route HTTP `/buffer/*` dan `/webhook/*` tetap jalan **di subdomain company**.

## 7. Branding

Isi nama & logo di tabel `sistems` per `company_id` (kolom `nama`, `isi`, `type`).

---

## Catatan penting

- Isolasi data lewat Eloquent global scope (`BelongsToCompany`). Query
  `DB::table()` mentah **tidak** otomatis ter-filter — service utama sudah
  dipatch dengan `company_where()`.
- Email user unik per company: `(company_id, email)`.
- Role/permission Spatie tetap katalog global; user terikat satu company.
- Login di subdomain A dengan akun company B → 403.
- Setelah install pertama, ganti password admin default.

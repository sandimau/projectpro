-- =============================================================================
-- REFACTOR PRODUK STOK SALDO
-- Setara dengan: database/migrations/2026_06_17_100000_refactor_produk_stok_saldo.php
--
-- Cara pakai di Hostinger (phpMyAdmin):
--   1. BACKUP database dulu (Export)
--   2. Jalankan per BLOK (urutan 0 → 8), satu per satu
--   3. Jika ada error "sudah ada" / "tidak ada kolom" → lewati langkah itu
--   4. Setelah selesai, cek BLOK VERIFIKASI di bawah
--
-- Kompatibel MySQL 5.7+ / MariaDB (tanpa window function)
-- =============================================================================


-- =============================================================================
-- BLOK 0 — PREVIEW (hanya baca, tidak mengubah data)
-- =============================================================================

-- Berapa baris duplikat yang akan dihapus?
SELECT COUNT(*) AS baris_duplikat_akan_dihapus
FROM produk_stoks s1
INNER JOIN produk_stoks s2
    ON s1.produk_id <=> s2.produk_id
    AND s1.kode <=> s2.kode
    AND s1.detail_id <=> s2.detail_id
    AND s1.tambah <=> s2.tambah
    AND s1.kurang <=> s2.kurang
    AND DATE(s1.created_at) = DATE(s2.created_at)
    AND s1.deleted_at IS NULL
    AND s2.deleted_at IS NULL
    AND s1.id > s2.id;

-- Cek apakah kolom saldo masih ada di produk_stoks
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'produk_stoks'
  AND COLUMN_NAME = 'saldo';

-- Cek apakah kolom tahun sudah ada di produk_last_stoks
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'produk_last_stoks'
  AND COLUMN_NAME = 'tahun';


-- =============================================================================
-- BLOK 1 — Hapus duplikat di produk_stoks (sisakan id terkecil)
-- =============================================================================

DELETE s1 FROM produk_stoks s1
INNER JOIN produk_stoks s2
    ON s1.produk_id <=> s2.produk_id
    AND s1.kode <=> s2.kode
    AND s1.detail_id <=> s2.detail_id
    AND s1.tambah <=> s2.tambah
    AND s1.kurang <=> s2.kurang
    AND DATE(s1.created_at) = DATE(s2.created_at)
    AND s1.deleted_at IS NULL
    AND s2.deleted_at IS NULL
    AND s1.id > s2.id;


-- =============================================================================
-- BLOK 2 — Tambah kolom tahun di produk_last_stoks
-- Lewati jika BLOK 0 sudah menunjukkan kolom tahun ada
-- =============================================================================

ALTER TABLE produk_last_stoks
    ADD COLUMN tahun INT NULL AFTER saldo;


-- =============================================================================
-- BLOK 3 — Isi tahun kosong dengan tahun berjalan (ganti 2026 jika perlu)
-- =============================================================================

UPDATE produk_last_stoks
SET tahun = YEAR(CURDATE())
WHERE tahun IS NULL;


-- =============================================================================
-- BLOK 4 — Kosongkan cache lalu rebuild saldo per produk per tahun
-- Rumus: saldo(tahun) = total mutasi dari awal s/d akhir tahun itu
-- =============================================================================

TRUNCATE TABLE produk_last_stoks;

INSERT INTO produk_last_stoks (produk_id, saldo, tahun, created_at, updated_at)
SELECT
    y.produk_id,
    (
        SELECT COALESCE(SUM(COALESCE(s.tambah, 0) - COALESCE(s.kurang, 0)), 0)
        FROM produk_stoks s
        WHERE s.produk_id = y.produk_id
          AND s.deleted_at IS NULL
          AND YEAR(s.created_at) <= y.tahun
    ) AS saldo,
    y.tahun,
    NOW(),
    NOW()
FROM (
    SELECT DISTINCT produk_id, YEAR(created_at) AS tahun
    FROM produk_stoks
    WHERE deleted_at IS NULL
) AS y
ORDER BY y.produk_id, y.tahun;


-- =============================================================================
-- BLOK 5 — Hapus kolom saldo dari produk_stoks (sudah tidak dipakai aplikasi)
-- Lewati jika BLOK 0 menunjukkan kolom saldo sudah tidak ada
-- =============================================================================

ALTER TABLE produk_stoks DROP COLUMN saldo;


-- =============================================================================
-- BLOK 6 — Unique index: satu baris cache per produk per tahun
-- Lewati jika error "Duplicate key name" (index sudah ada)
-- =============================================================================

ALTER TABLE produk_last_stoks
    ADD UNIQUE INDEX produk_last_stoks_produk_tahun_unique (produk_id, tahun);


-- =============================================================================
-- BLOK 7 — Tandai migration Laravel sudah jalan (supaya artisan migrate tidak ulang)
-- Lewati jika baris ini sudah ada di tabel migrations
-- =============================================================================

INSERT INTO migrations (migration, batch)
SELECT
    '2026_06_17_100000_refactor_produk_stok_saldo',
    COALESCE(MAX(batch), 0) + 1
FROM migrations
WHERE NOT EXISTS (
    SELECT 1 FROM migrations
    WHERE migration = '2026_06_17_100000_refactor_produk_stok_saldo'
);


-- =============================================================================
-- BLOK VERIFIKASI — Jalankan setelah semua blok di atas
-- =============================================================================

-- 1) Tidak boleh ada duplikat (produk_id + tahun)
SELECT produk_id, tahun, COUNT(*) AS jumlah
FROM produk_last_stoks
GROUP BY produk_id, tahun
HAVING COUNT(*) > 1;

-- 2) Bandingkan cache vs hitung langsung (harus sama untuk tahun berjalan)
SELECT
    p.id AS produk_id,
    pls.saldo AS cache_saldo,
    (
        SELECT COALESCE(SUM(COALESCE(s.tambah, 0) - COALESCE(s.kurang, 0)), 0)
        FROM produk_stoks s
        WHERE s.produk_id = p.id
          AND s.deleted_at IS NULL
          AND YEAR(s.created_at) = YEAR(CURDATE())
    )
    + COALESCE((
        SELECT pls2.saldo
        FROM produk_last_stoks pls2
        WHERE pls2.produk_id = p.id
          AND pls2.tahun < YEAR(CURDATE())
        ORDER BY pls2.tahun DESC
        LIMIT 1
    ), 0) AS hitung_ulang_saldo
FROM produks p
LEFT JOIN produk_last_stoks pls
    ON pls.produk_id = p.id
   AND pls.tahun = YEAR(CURDATE())
WHERE EXISTS (
    SELECT 1 FROM produk_stoks s
    WHERE s.produk_id = p.id AND s.deleted_at IS NULL
)
HAVING cache_saldo IS NOT NULL
   AND cache_saldo <> hitung_ulang_saldo
LIMIT 20;

-- 3) Pastikan kolom saldo sudah hilang dari produk_stoks
SHOW COLUMNS FROM produk_stoks LIKE 'saldo';

-- 4) Pastikan migration tercatat
SELECT * FROM migrations
WHERE migration = '2026_06_17_100000_refactor_produk_stok_saldo';

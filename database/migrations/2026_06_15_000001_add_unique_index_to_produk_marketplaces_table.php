<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * produk_marketplaces diisi lewat ProdukMarketplace::upsert() saat order diproses.
     * upsert() di MySQL hanya meng-update bila ada UNIQUE index pada kolom kuncinya.
     * Tanpa index ini, setiap order baru menambah baris duplikat (produk yang sama
     * muncul berkali-kali). Migrasi ini membersihkan duplikat lalu menambah unique index.
     */
    private string $indexName = 'produk_marketplaces_marketplace_item_model_unique';

    private array $columns = ['marketplace_id', 'item_id', 'model_id'];

    public function up(): void
    {
        if (!Schema::hasTable('produk_marketplaces')) {
            return;
        }

        // 1) Hapus baris duplikat, sisakan id terbesar (paling baru) per kombinasi
        DB::statement('
            DELETE pm1 FROM produk_marketplaces pm1
            INNER JOIN produk_marketplaces pm2
                ON pm1.marketplace_id = pm2.marketplace_id
                AND pm1.item_id = pm2.item_id
                AND pm1.model_id = pm2.model_id
                AND pm1.id < pm2.id
        ');

        // 2) Tambah unique index hanya jika belum ada (idempoten:
        //    aman walau index sudah dibuat manual, dengan nama apa pun)
        if (!$this->uniqueIndexOnColumnsExists($this->columns)) {
            Schema::table('produk_marketplaces', function (Blueprint $table) {
                $table->unique($this->columns, $this->indexName);
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('produk_marketplaces')) {
            return;
        }

        if ($this->indexExists($this->indexName)) {
            Schema::table('produk_marketplaces', function (Blueprint $table) {
                $table->dropUnique($this->indexName);
            });
        }
    }

    private function indexExists(string $name): bool
    {
        return !empty(DB::select(
            'SHOW INDEX FROM produk_marketplaces WHERE Key_name = ?',
            [$name]
        ));
    }

    /**
     * Cek apakah sudah ada UNIQUE index yang persis menutupi kombinasi kolom ini,
     * dengan nama apa pun, agar tidak membuat index dobel.
     */
    private function uniqueIndexOnColumnsExists(array $columns): bool
    {
        $rows = DB::select('SHOW INDEX FROM produk_marketplaces WHERE Non_unique = 0');

        $byKey = [];
        foreach ($rows as $row) {
            $byKey[$row->Key_name][(int) $row->Seq_in_index] = $row->Column_name;
        }

        foreach ($byKey as $cols) {
            ksort($cols);
            if (array_values($cols) === $columns) {
                return true;
            }
        }

        return false;
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel tenant yang mendapat company_id.
     * Skip: users (sudah), companies, failed_jobs, password_*, personal_access_tokens, Spatie.
     */
    protected array $tables = [
        'orders',
        'order_details',
        'order_jadwals',
        'order_speks',
        'kontaks',
        'pembayarans',
        'chats',
        'whattodos',
        'produks',
        'produk_kategoris',
        'produk_kategori_utamas',
        'produk_models',
        'produk_last_stoks',
        'produk_stoks',
        'produk_pakais',
        'produk_marketplaces',
        'produk_produksis',
        'produk_produksi_hasils',
        'produk_produksi_bahans',
        'produk_produksi_belanja',
        'produk_po',
        'produk_po_detail',
        'produk_po_belanja',
        'po_deposit',
        'speks',
        'proses',
        'levels',
        'bagians',
        'produksis',
        'produksi_produks',
        'belanjas',
        'belanja_details',
        'hutangs',
        'hutang_details',
        'akuns',
        'akun_kategoris',
        'akun_details',
        'akun_last_saldos',
        'buku_besars',
        'members',
        'cutis',
        'lemburs',
        'kasbons',
        'gajis',
        'penggajians',
        'tunjangans',
        'absensis',
        'attendances',
        'ars',
        'freelance_tagihans',
        'user_devices',
        'marketplaces',
        'marketplace_buffers',
        'marketplace_formats',
        'marketplace_logs',
        'project_mps',
        'project_mp_details',
        'shopee_stock_syncs',
        'sistems',
        'link_pages',
        'link_items',
        'pemproses',
    ];

    public function up(): void
    {
        // Company default untuk backfill data existing
        $defaultId = DB::table('companies')->where('slug', env('DEFAULT_COMPANY_SLUG', 'default'))->value('id');

        if (! $defaultId) {
            $defaultId = DB::table('companies')->insertGetId([
                'name' => env('APP_NAME', 'Default Company'),
                'slug' => env('DEFAULT_COMPANY_SLUG', 'default'),
                'is_active' => true,
                'settings' => json_encode([
                    'office_latitude' => env('OFFICE_LATITUDE', -6.8508137608568),
                    'office_longitude' => env('OFFICE_LONGITUDE', 107.63763214234),
                    'max_distance_radius' => (int) env('MAX_DISTANCE_RADIUS', 30000),
                    'clock_in_time' => env('CLOCK_IN_TIME', '08:00'),
                    'clock_out_time' => env('CLOCK_OUT_TIME', '17:00'),
                    'late_tolerance_minutes' => (int) env('LATE_TOLERANCE_MINUTES', 0),
                    'fonnte_token' => env('FONNTE_TOKEN'),
                    'whatsapp_group_target' => env('WHATSAPP_GROUP_TARGET'),
                    'qr_code_secret' => env('QR_CODE_SECRET', 'MANDIRI-MOTOR-SECRET-CODE'),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (! Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->unsignedBigInteger('company_id')->nullable()->index();
                });
            }

            // Backfill null / 0
            DB::table($table)->whereNull('company_id')->update(['company_id' => $defaultId]);
            if (Schema::hasColumn($table, 'company_id')) {
                // Beberapa kolom lama integer 0
                DB::table($table)->where('company_id', 0)->update(['company_id' => $defaultId]);
            }
        }

        // Users tanpa company
        DB::table('users')->whereNull('company_id')->update(['company_id' => $defaultId]);

        // FK (abaikan jika sudah ada / engine tidak support)
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'company_id')) {
                continue;
            }

            try {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    $blueprint->foreign('company_id', $this->fkName($table))
                        ->references('id')
                        ->on('companies')
                        ->cascadeOnDelete();
                });
            } catch (\Throwable $e) {
                // FK mungkin sudah ada atau tipe kolom lama beda — lanjut
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'company_id')) {
                continue;
            }

            try {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    $blueprint->dropForeign($this->fkName($table));
                });
            } catch (\Throwable $e) {
                // ignore
            }

            // Jangan drop company_id di tabel yang memang sudah punya sejak create
            $preExisting = ['marketplace_logs', 'project_mp_details', 'produksi_produks'];
            if (in_array($table, $preExisting, true)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropColumn('company_id');
            });
        }
    }

    protected function fkName(string $table): string
    {
        return substr('fk_'.$table.'_company_id', 0, 64);
    }
};

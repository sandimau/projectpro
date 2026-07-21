<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('marketplace_buffers') && Schema::hasColumn('marketplace_buffers', 'company_id')) {
            $this->dropIndexIfExists('marketplace_buffers', 'marketplace_buffers_nota_unique');
            $this->dropIndexIfExists('marketplace_buffers', 'idx_nota_unique');

            if (! $this->indexExists('marketplace_buffers', 'marketplace_buffers_company_nota_unique')) {
                Schema::table('marketplace_buffers', function (Blueprint $table) {
                    $table->unique(['company_id', 'nota'], 'marketplace_buffers_company_nota_unique');
                });
            }
        }

        if (Schema::hasTable('sistems') && Schema::hasColumn('sistems', 'company_id')) {
            if (! $this->indexExists('sistems', 'sistems_company_nama_unique')) {
                Schema::table('sistems', function (Blueprint $table) {
                    $table->unique(['company_id', 'nama'], 'sistems_company_nama_unique');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('marketplace_buffers')) {
            $this->dropIndexIfExists('marketplace_buffers', 'marketplace_buffers_company_nota_unique');

            if (! $this->indexExists('marketplace_buffers', 'marketplace_buffers_nota_unique')) {
                Schema::table('marketplace_buffers', function (Blueprint $table) {
                    $table->unique('nota', 'marketplace_buffers_nota_unique');
                });
            }
        }

        if (Schema::hasTable('sistems')) {
            $this->dropIndexIfExists('sistems', 'sistems_company_nama_unique');
        }
    }

    protected function indexExists(string $table, string $index): bool
    {
        $db = DB::getDatabaseName();
        $row = DB::selectOne(
            'SELECT COUNT(*) AS c FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$db, $table, $index]
        );

        return ((int) ($row->c ?? 0)) > 0;
    }

    protected function dropIndexIfExists(string $table, string $index): void
    {
        if ($this->indexExists($table, $index)) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
        }
    }
};

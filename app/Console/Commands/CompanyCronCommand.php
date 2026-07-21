<?php

namespace App\Console\Commands;

use App\Http\Controllers\Webhook\BufferController;
use App\Http\Controllers\Webhook\ShopeeLivePushController;
use App\Http\Controllers\Webhook\ShopeeStockSyncController;
use App\Support\Tenant;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class CompanyCronCommand extends Command
{
    protected $signature = 'company:cron
                            {job : Job yang dijalankan (buffer-proses|buffer-pending|buffer-wallet|buffer-update|sync-stok|refresh-token)}
                            {--company= : Slug company saja (opsional)}';

    protected $description = 'Jalankan job cron marketplace/buffer untuk setiap company (atau satu slug)';

    public function handle(): int
    {
        $job = $this->argument('job');
        $slug = $this->option('company');

        $companies = \App\Models\Company::query()->active()->orderBy('id');
        if ($slug) {
            $companies->where('slug', $slug);
        }

        $list = $companies->get();
        if ($list->isEmpty()) {
            $this->warn('Tidak ada company.');

            return self::FAILURE;
        }

        foreach ($list as $company) {
            $this->info("[{$company->slug}] menjalankan {$job}...");
            Tenant::runFor($company, function () use ($job, $company) {
                try {
                    $this->dispatchJob($job);
                } catch (\Throwable $e) {
                    $this->error("[{$company->slug}] {$e->getMessage()}");
                    report($e);
                }
            });
        }

        return self::SUCCESS;
    }

    protected function dispatchJob(string $job): void
    {
        $request = Request::create('/', 'GET');

        match ($job) {
            'buffer-proses' => app(BufferController::class)->prosesBuffer(),
            'buffer-pending' => app(BufferController::class)->cekBufferPending(),
            'buffer-wallet' => app(BufferController::class)->wallet(),
            'buffer-update' => app(BufferController::class)->updateBuffer(),
            'sync-stok' => app(ShopeeStockSyncController::class)->sync($request),
            'refresh-token' => app(ShopeeLivePushController::class)->manualRefreshToken(),
            default => throw new \InvalidArgumentException("Job tidak dikenal: {$job}"),
        };
    }
}

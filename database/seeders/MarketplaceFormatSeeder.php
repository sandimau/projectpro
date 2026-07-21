<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketplaceFormatSeeder extends Seeder
{
    /**
     * Seed struktur format marketplace per company.
     * Kredensial Open Platform diisi manual setelah seeding.
     */
    public function run(): void
    {
        $companyId = current_company_id()
            ?? \App\Models\Company::query()->where('slug', config('tenancy.default_company_slug', 'default'))->value('id')
            ?? \App\Models\Company::query()->orderBy('id')->value('id');

        if (! $companyId) {
            $this->command?->warn('MarketplaceFormatSeeder dilewati: belum ada company.');

            return;
        }

        $this->seedForCompany((int) $companyId);
    }

    public function seedForCompany(int $companyId): void
    {
        $rows = [
            ['marketplace' => 'shopee', 'jenis' => 'order', 'kolom1' => 'No. Pesanan', 'kolom2' => 'Status Pesanan', 'kolom3' => 'Alasan Pembatalan', 'nota' => 1, 'status' => 2, 'tanggal' => 10, 'nama' => 43, 'sku' => 13, 'sku_anak' => 15, 'jumlah' => 19, 'harga' => 18, 'tema' => 16, 'saldo' => 39, 'barisHeader' => null, 'formatTanggal' => 'Y-m-d H:i', 'produk' => 14, 'batal' => 'Batal', 'ongkir' => 40, 'deathline' => 8],
            ['marketplace' => 'shopee', 'jenis' => 'keuangan', 'kolom1' => 'Tanggal Transaksi', 'kolom2' => 'Tipe Transaksi', 'kolom3' => 'Deskripsi', 'nota' => null, 'status' => null, 'tanggal' => 1, 'nama' => null, 'sku' => null, 'sku_anak' => null, 'jumlah' => null, 'harga' => 6, 'tema' => 3, 'saldo' => 8, 'barisHeader' => 18, 'formatTanggal' => 'Y-m-d H:i:s', 'produk' => null, 'batal' => 'Penarikan Dana', 'ongkir' => null, 'deathline' => null],
            ['marketplace' => 'shopee', 'jenis' => 'stok', 'kolom1' => 'Kode Produk', 'kolom2' => 'Nama Produk', 'kolom3' => 'Kode Variasi', 'nota' => 1, 'status' => 4, 'tanggal' => 3, 'nama' => null, 'sku' => 5, 'sku_anak' => 6, 'jumlah' => 8, 'harga' => 7, 'tema' => 4, 'saldo' => 8, 'barisHeader' => 3, 'formatTanggal' => null, 'produk' => 2, 'batal' => null, 'ongkir' => null, 'deathline' => null],
            ['marketplace' => 'tokopedia', 'jenis' => 'order', 'kolom1' => 'Nomor', 'kolom2' => 'Nomor Invoice', 'kolom3' => 'Tanggal Pembayaran', 'nota' => 2, 'status' => 4, 'tanggal' => 3, 'nama' => 27, 'sku' => 11, 'sku_anak' => 11, 'jumlah' => 14, 'harga' => 15, 'tema' => 9, 'saldo' => 26, 'barisHeader' => 5, 'formatTanggal' => 'd-m-Y H:i:s', 'produk' => 9, 'batal' => 'batal', 'ongkir' => null, 'deathline' => null],
            ['marketplace' => 'tokopedia', 'jenis' => 'keuangan', 'kolom1' => 'Date', 'kolom2' => 'Mutation (Debit/Credit)', 'kolom3' => 'Description', 'nota' => null, 'status' => null, 'tanggal' => 1, 'nama' => null, 'sku' => null, 'sku_anak' => null, 'jumlah' => null, 'harga' => 4, 'tema' => 3, 'saldo' => 5, 'barisHeader' => 7, 'formatTanggal' => 'Y-m-d H:i:s', 'produk' => null, 'batal' => 'Withdrawal', 'ongkir' => null, 'deathline' => null],
            ['marketplace' => 'tokopedia', 'jenis' => 'stok', 'kolom1' => 'Pesan Error', 'kolom2' => 'Product ID', 'kolom3' => 'Nama Produk', 'nota' => null, 'status' => 2, 'tanggal' => null, 'nama' => null, 'sku' => 11, 'sku_anak' => null, 'jumlah' => 9, 'harga' => 8, 'tema' => null, 'saldo' => 6, 'barisHeader' => 2, 'formatTanggal' => null, 'produk' => 3, 'batal' => null, 'ongkir' => null, 'deathline' => null],
            ['marketplace' => 'tiktok', 'jenis' => 'order', 'kolom1' => 'Order ID', 'kolom2' => 'Order Status', 'kolom3' => 'Order Substatus', 'nota' => 1, 'status' => 3, 'tanggal' => 28, 'nama' => 42, 'sku' => 7, 'sku_anak' => 7, 'jumlah' => 10, 'harga' => 12, 'tema' => 8, 'saldo' => 26, 'barisHeader' => null, 'formatTanggal' => 'd/m/Y H:i:s', 'produk' => 13, 'batal' => 'Dibatalkan', 'ongkir' => 17, 'deathline' => null],
            ['marketplace' => 'tiktok', 'jenis' => 'keuangan', 'kolom1' => 'Order/adjustment ID  ', 'kolom2' => 'Type ', 'kolom3' => 'Order created time', 'nota' => null, 'status' => null, 'tanggal' => 3, 'nama' => null, 'sku' => null, 'sku_anak' => null, 'jumlah' => null, 'harga' => 6, 'tema' => 2, 'saldo' => 6, 'barisHeader' => null, 'formatTanggal' => 'Y/md/', 'produk' => null, 'batal' => 'Penarikan', 'ongkir' => null, 'deathline' => null],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('marketplace_formats')
                ->where('company_id', $companyId)
                ->where('marketplace', $row['marketplace'])
                ->where('jenis', $row['jenis'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('marketplace_formats')->insert(array_merge($row, [
                'company_id' => $companyId,
                'partnerId' => 0,
                'partnerKey' => '',
                'host' => $row['marketplace'] === 'shopee' ? 'https://partner.shopeemobile.com/' : '',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}

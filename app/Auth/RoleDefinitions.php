<?php

namespace App\Auth;

class RoleDefinitions
{
    public const SUPER = 'super';
    public const ADMIN = 'admin';
    public const USER = 'user';

    /**
     * @return array<string, list<string>|string>
     */
    public static function definitions(): array
    {
        return [
            self::SUPER => '*',
            self::ADMIN => [
                'order_proses_access',
                'order_proses_create',
                'order_proses_edit',
                'order_offline_access',
                'order_online_access',
                'order_detail_access',
                'order_detail_create',
                'order_detail_edit',
                'mp_custom_access',
                'mp_packing_access',
                'mp_arsip_access',
                'mp_produk_access',
                'mp_config_access',
                'mp_config_create',
                'mp_config_edit',
                'mp_sync_access',
                'mp_analisa_access',
                'kontak_access',
                'kontak_create',
                'kontak_edit',
                'kas_access',
                'belum_lunas_access',
                'belanja_access',
                'belanja_create',
                'hutang_access',
                'produk_access',
                'pemakaian_access',
                'pemakaian_create',
                'pemakaian_edit',
                'opname_access',
                'po_access',
                'produk_stok_access',
                'produk_stok_create',
                'produksi_proses_access',
                'produksi_produk_access',
                'member_access',
                'member_create',
                'member_edit',
                'freelance_access',
                'absensi_access',
                'absensi_scan',
                'ar_access',
                'ar_create',
                'cuti_access',
                'cuti_create',
                'kasbon_access',
                'kasbon_create',
                'lembur_access',
                'tunjangan_access',
                'penggajian_access',
                'gaji_access',
                'gaji_create',
                'gaji_edit',
                'analisa_beban_access',
                'analisa_operasional_access',
                'analisa_stok_access',
                'laporan_tunjangan_access',
                'laporan_penggajian_access',
                'laporan_neraca_access',
                'laporan_labarugi_access',
                'omzet_tahunan_access',
                'omzet_bulanan_access',
                'omzet_marketplace_access',
                'omzet_aset_access',
                'omzet_produk_access',
                'user_access',
                'user_create',
                'level_access',
                'level_create',
                'bagian_access',
                'sistem_access',
            ],
            self::USER => [
                'order_proses_access',
                'order_offline_access',
                'order_online_access',
                'order_detail_access',
                'kontak_access',
                'produk_access',
                'produk_stok_access',
                'absensi_scan',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function roleNames(): array
    {
        return array_keys(self::definitions());
    }

    /**
     * @return list<string>
     */
    public static function permissionsFor(string $role): array
    {
        $defs = self::definitions();

        if (! isset($defs[$role])) {
            return [];
        }

        if ($defs[$role] === '*') {
            return Permissions::all();
        }

        return $defs[$role];
    }
}

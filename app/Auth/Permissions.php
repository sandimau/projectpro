<?php

namespace App\Auth;

class Permissions
{
    public const GUARD = 'web';

    public const RBAC_MANAGE = 'rbac.manage';

    /** @var list<string> */
    public const CRUD_ACTIONS = ['read', 'create', 'update', 'delete'];

    /**
     * Katalog permission mengikuti struktur sidebar navigation.
     * group = menu induk, label = submenu detail.
     *
     * @return array<string, array{
     *   label: string,
     *   group: string,
     *   actions: array{read?: ?string, create?: ?string, update?: ?string, delete?: ?string},
     *   extras?: array<string, string>
     * }>
     */
    public static function menus(): array
    {
        return [
            // ── Proses Order ──────────────────────────────────────────
            'order_proses' => [
                'label' => 'Proses',
                'group' => 'Proses Order',
                'actions' => [
                    'read' => 'order_proses_access',
                    'create' => 'order_proses_create',
                    'update' => 'order_proses_edit',
                    'delete' => 'order_proses_delete',
                ],
            ],
            'order_offline' => [
                'label' => 'Arsip Offline',
                'group' => 'Proses Order',
                'actions' => [
                    'read' => 'order_offline_access',
                    'create' => 'order_offline_create',
                    'update' => 'order_offline_edit',
                    'delete' => 'order_offline_delete',
                ],
            ],
            'order_online' => [
                'label' => 'Arsip Online',
                'group' => 'Proses Order',
                'actions' => [
                    'read' => 'order_online_access',
                    'create' => 'order_online_create',
                    'update' => 'order_online_edit',
                    'delete' => 'order_online_delete',
                ],
            ],
            'order_detail' => [
                'label' => 'Detail Order',
                'group' => 'Proses Order',
                'actions' => [
                    'read' => 'order_detail_access',
                    'create' => 'order_detail_create',
                    'update' => 'order_detail_edit',
                    'delete' => 'order_detail_delete',
                ],
            ],

            // ── Marketplace ───────────────────────────────────────────
            'mp_custom' => [
                'label' => 'Proses Custom',
                'group' => 'Marketplace',
                'actions' => [
                    'read' => 'mp_custom_access',
                    'create' => 'mp_custom_create',
                    'update' => 'mp_custom_edit',
                    'delete' => 'mp_custom_delete',
                ],
            ],
            'mp_packing' => [
                'label' => 'Proses Packing',
                'group' => 'Marketplace',
                'actions' => [
                    'read' => 'mp_packing_access',
                    'create' => 'mp_packing_create',
                    'update' => 'mp_packing_edit',
                    'delete' => 'mp_packing_delete',
                ],
            ],
            'mp_arsip' => [
                'label' => 'Arsip Order',
                'group' => 'Marketplace',
                'actions' => [
                    'read' => 'mp_arsip_access',
                    'create' => 'mp_arsip_create',
                    'update' => 'mp_arsip_edit',
                    'delete' => 'mp_arsip_delete',
                ],
            ],
            'mp_produk' => [
                'label' => 'Produk',
                'group' => 'Marketplace',
                'actions' => [
                    'read' => 'mp_produk_access',
                    'create' => 'mp_produk_create',
                    'update' => 'mp_produk_edit',
                    'delete' => 'mp_produk_delete',
                ],
            ],
            'mp_config' => [
                'label' => 'Config',
                'group' => 'Marketplace',
                'actions' => [
                    'read' => 'mp_config_access',
                    'create' => 'mp_config_create',
                    'update' => 'mp_config_edit',
                    'delete' => 'mp_config_delete',
                ],
            ],
            'mp_sync' => [
                'label' => 'Sync Stok Shopee',
                'group' => 'Marketplace',
                'actions' => [
                    'read' => 'mp_sync_access',
                    'create' => null,
                    'update' => 'mp_sync_edit',
                    'delete' => null,
                ],
            ],
            'mp_analisa' => [
                'label' => 'Analisa',
                'group' => 'Marketplace',
                'actions' => [
                    'read' => 'mp_analisa_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],

            // ── Data ──────────────────────────────────────────────────
            'kontak' => [
                'label' => 'Kontak',
                'group' => 'Data',
                'actions' => [
                    'read' => 'kontak_access',
                    'create' => 'kontak_create',
                    'update' => 'kontak_edit',
                    'delete' => 'kontak_delete',
                ],
            ],

            // ── Keuangan ──────────────────────────────────────────────
            'akun' => [
                'label' => 'Akun',
                'group' => 'Keuangan',
                'actions' => [
                    'read' => 'akun_access',
                    'create' => 'akun_create',
                    'update' => 'akun_edit',
                    'delete' => 'akun_delete',
                ],
            ],
            'akun_kategori' => [
                'label' => 'Akun Kategori',
                'group' => 'Keuangan',
                'actions' => [
                    'read' => 'akun_kategori_access',
                    'create' => 'akun_kategori_create',
                    'update' => 'akun_kategori_edit',
                    'delete' => 'akun_kategori_delete',
                ],
            ],
            'kas' => [
                'label' => 'Kas',
                'group' => 'Keuangan',
                'actions' => [
                    'read' => 'kas_access',
                    'create' => 'kas_create',
                    'update' => 'kas_edit',
                    'delete' => 'kas_delete',
                ],
            ],
            'belum_lunas' => [
                'label' => 'Belum Lunas',
                'group' => 'Keuangan',
                'actions' => [
                    'read' => 'belum_lunas_access',
                    'create' => null,
                    'update' => 'belum_lunas_edit',
                    'delete' => null,
                ],
            ],
            'belanja' => [
                'label' => 'Belanja',
                'group' => 'Keuangan',
                'actions' => [
                    'read' => 'belanja_access',
                    'create' => 'belanja_create',
                    'update' => 'belanja_edit',
                    'delete' => 'belanja_delete',
                ],
            ],
            'hutang' => [
                'label' => 'Hutang/Piutang',
                'group' => 'Keuangan',
                'actions' => [
                    'read' => 'hutang_access',
                    'create' => 'hutang_create',
                    'update' => 'hutang_edit',
                    'delete' => 'hutang_delete',
                ],
            ],

            // ── Inventory ─────────────────────────────────────────────
            'produk' => [
                'label' => 'Produk',
                'group' => 'Inventory',
                'actions' => [
                    'read' => 'produk_access',
                    'create' => 'produk_create',
                    'update' => 'produk_edit',
                    'delete' => 'produk_delete',
                ],
            ],
            'pemakaian' => [
                'label' => 'Pemakaian',
                'group' => 'Inventory',
                'actions' => [
                    'read' => 'pemakaian_access',
                    'create' => 'pemakaian_create',
                    'update' => 'pemakaian_edit',
                    'delete' => 'pemakaian_delete',
                ],
            ],
            'opname' => [
                'label' => 'Opname',
                'group' => 'Inventory',
                'actions' => [
                    'read' => 'opname_access',
                    'create' => 'opname_create',
                    'update' => 'opname_edit',
                    'delete' => 'opname_delete',
                ],
            ],
            'po' => [
                'label' => 'PO',
                'group' => 'Inventory',
                'actions' => [
                    'read' => 'po_access',
                    'create' => 'po_create',
                    'update' => 'po_edit',
                    'delete' => 'po_delete',
                ],
            ],
            'produk_stok' => [
                'label' => 'Stok Produk',
                'group' => 'Inventory',
                'actions' => [
                    'read' => 'produk_stok_access',
                    'create' => 'produk_stok_create',
                    'update' => 'produk_stok_edit',
                    'delete' => 'produk_stok_delete',
                ],
            ],

            // ── Produksi ──────────────────────────────────────────────
            'produksi_proses' => [
                'label' => 'Proses',
                'group' => 'Produksi',
                'actions' => [
                    'read' => 'produksi_proses_access',
                    'create' => 'produksi_proses_create',
                    'update' => 'produksi_proses_edit',
                    'delete' => 'produksi_proses_delete',
                ],
            ],
            'produksi_produk' => [
                'label' => 'Produk',
                'group' => 'Produksi',
                'actions' => [
                    'read' => 'produksi_produk_access',
                    'create' => 'produksi_produk_create',
                    'update' => 'produksi_produk_edit',
                    'delete' => 'produksi_produk_delete',
                ],
            ],

            // ── Pegawai ───────────────────────────────────────────────
            'karyawan' => [
                'label' => 'Karyawan',
                'group' => 'Pegawai',
                'actions' => [
                    'read' => 'member_access',
                    'create' => 'member_create',
                    'update' => 'member_edit',
                    'delete' => 'member_delete',
                ],
            ],
            'freelance' => [
                'label' => 'Freelance',
                'group' => 'Pegawai',
                'actions' => [
                    'read' => 'freelance_access',
                    'create' => 'freelance_create',
                    'update' => 'freelance_edit',
                    'delete' => 'freelance_delete',
                ],
            ],
            'absensi' => [
                'label' => 'Absensi',
                'group' => 'Pegawai',
                'actions' => [
                    'read' => 'absensi_access',
                    'create' => 'absensi_create',
                    'update' => 'absensi_edit',
                    'delete' => 'absensi_delete',
                ],
            ],
            'cs' => [
                'label' => 'CS',
                'group' => 'Pegawai',
                'actions' => [
                    'read' => 'ar_access',
                    'create' => 'ar_create',
                    'update' => 'ar_edit',
                    'delete' => 'ar_delete',
                ],
            ],
            'cuti' => [
                'label' => 'Cuti / Ijin',
                'group' => 'Pegawai',
                'actions' => [
                    'read' => 'cuti_access',
                    'create' => 'cuti_create',
                    'update' => 'cuti_edit',
                    'delete' => 'cuti_delete',
                ],
            ],
            'kasbon' => [
                'label' => 'Kasbon',
                'group' => 'Pegawai',
                'actions' => [
                    'read' => 'kasbon_access',
                    'create' => 'kasbon_create',
                    'update' => 'kasbon_edit',
                    'delete' => 'kasbon_delete',
                ],
            ],
            'lembur' => [
                'label' => 'Lembur',
                'group' => 'Pegawai',
                'actions' => [
                    'read' => 'lembur_access',
                    'create' => 'lembur_create',
                    'update' => 'lembur_edit',
                    'delete' => 'lembur_delete',
                ],
            ],
            'tunjangan' => [
                'label' => 'Tunjangan',
                'group' => 'Pegawai',
                'actions' => [
                    'read' => 'tunjangan_access',
                    'create' => 'tunjangan_create',
                    'update' => 'tunjangan_edit',
                    'delete' => 'tunjangan_delete',
                ],
            ],
            'penggajian' => [
                'label' => 'Penggajian',
                'group' => 'Pegawai',
                'actions' => [
                    'read' => 'penggajian_access',
                    'create' => 'penggajian_create',
                    'update' => 'penggajian_edit',
                    'delete' => 'penggajian_delete',
                ],
            ],
            'gaji' => [
                'label' => 'Master Gaji',
                'group' => 'Pegawai',
                'actions' => [
                    'read' => 'gaji_access',
                    'create' => 'gaji_create',
                    'update' => 'gaji_edit',
                    'delete' => 'gaji_delete',
                ],
            ],

            // ── Analisa ───────────────────────────────────────────────
            'analisa_beban' => [
                'label' => 'Analisa Beban',
                'group' => 'Analisa',
                'actions' => [
                    'read' => 'analisa_beban_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],
            'analisa_operasional' => [
                'label' => 'Analisa Operasional',
                'group' => 'Analisa',
                'actions' => [
                    'read' => 'analisa_operasional_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],
            'analisa_stok' => [
                'label' => 'Analisa Stok',
                'group' => 'Analisa',
                'actions' => [
                    'read' => 'analisa_stok_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],

            // ── Laporan ───────────────────────────────────────────────
            'laporan_tunjangan' => [
                'label' => 'Tunjangan',
                'group' => 'Laporan',
                'actions' => [
                    'read' => 'laporan_tunjangan_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],
            'laporan_penggajian' => [
                'label' => 'Penggajian',
                'group' => 'Laporan',
                'actions' => [
                    'read' => 'laporan_penggajian_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],
            'laporan_neraca' => [
                'label' => 'Neraca',
                'group' => 'Laporan',
                'actions' => [
                    'read' => 'laporan_neraca_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],
            'laporan_labarugi' => [
                'label' => 'Laba Rugi',
                'group' => 'Laporan',
                'actions' => [
                    'read' => 'laporan_labarugi_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],

            // ── Omzet ─────────────────────────────────────────────────
            'omzet_tahunan' => [
                'label' => 'Tahunan',
                'group' => 'Omzet',
                'actions' => [
                    'read' => 'omzet_tahunan_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],
            'omzet_bulanan' => [
                'label' => 'Bulanan',
                'group' => 'Omzet',
                'actions' => [
                    'read' => 'omzet_bulanan_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],
            'omzet_marketplace' => [
                'label' => 'Marketplace',
                'group' => 'Omzet',
                'actions' => [
                    'read' => 'omzet_marketplace_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],
            'omzet_aset' => [
                'label' => 'Aset',
                'group' => 'Omzet',
                'actions' => [
                    'read' => 'omzet_aset_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],
            'omzet_produk' => [
                'label' => 'Produk Omzet',
                'group' => 'Omzet',
                'actions' => [
                    'read' => 'omzet_produk_access',
                    'create' => null,
                    'update' => null,
                    'delete' => null,
                ],
            ],

            // ── User Management ───────────────────────────────────────
            'user' => [
                'label' => 'Users',
                'group' => 'User Management',
                'actions' => [
                    'read' => 'user_access',
                    'create' => 'user_create',
                    'update' => 'user_edit',
                    'delete' => 'user_delete',
                ],
            ],
            'level' => [
                'label' => 'Levels',
                'group' => 'User Management',
                'actions' => [
                    'read' => 'level_access',
                    'create' => 'level_create',
                    'update' => 'level_edit',
                    'delete' => 'level_delete',
                ],
            ],
            'bagian' => [
                'label' => 'Bagians',
                'group' => 'User Management',
                'actions' => [
                    'read' => 'bagian_access',
                    'create' => 'bagian_create',
                    'update' => 'bagian_edit',
                    'delete' => 'bagian_delete',
                ],
            ],

            // ── Config ────────────────────────────────────────────────
            'rbac' => [
                'label' => 'Roles & Akses',
                'group' => 'Config',
                'actions' => [
                    'read' => self::RBAC_MANAGE,
                    'create' => self::RBAC_MANAGE,
                    'update' => self::RBAC_MANAGE,
                    'delete' => self::RBAC_MANAGE,
                ],
            ],
            'setup_produksi' => [
                'label' => 'Setup Produksi',
                'group' => 'Config',
                'actions' => [
                    'read' => 'setup_produksi_access',
                    'create' => 'setup_produksi_create',
                    'update' => 'setup_produksi_edit',
                    'delete' => 'setup_produksi_delete',
                ],
            ],
            'spek' => [
                'label' => 'Spek Produk',
                'group' => 'Config',
                'actions' => [
                    'read' => 'spek_access',
                    'create' => 'spek_create',
                    'update' => 'spek_edit',
                    'delete' => 'spek_delete',
                ],
            ],
            'pemproses' => [
                'label' => 'Pemproses',
                'group' => 'Config',
                'actions' => [
                    'read' => 'pemproses_access',
                    'create' => 'pemproses_create',
                    'update' => 'pemproses_edit',
                    'delete' => 'pemproses_delete',
                ],
            ],
            'sistem' => [
                'label' => 'Sistem',
                'group' => 'Config',
                'actions' => [
                    'read' => 'sistem_access',
                    'create' => 'sistem_create',
                    'update' => 'sistem_edit',
                    'delete' => 'sistem_delete',
                ],
            ],
            'link_page' => [
                'label' => 'Link Pages',
                'group' => 'Config',
                'actions' => [
                    'read' => 'link_page_access',
                    'create' => 'link_page_create',
                    'update' => 'link_page_edit',
                    'delete' => 'link_page_delete',
                ],
            ],
        ];
    }

    /**
     * Permission read induk untuk tampilkan grup navigasi.
     *
     * @return array<string, list<string>>
     */
    public static function navGroupReads(): array
    {
        return [
            'proses_order' => [
                'order_proses_access',
                'order_offline_access',
                'order_online_access',
            ],
            'marketplace' => [
                'mp_custom_access',
                'mp_packing_access',
                'mp_arsip_access',
                'mp_produk_access',
                'mp_config_access',
                'mp_sync_access',
                'mp_analisa_access',
            ],
            'data' => [
                'kontak_access',
            ],
            'keuangan' => [
                'akun_access',
                'kas_access',
                'belum_lunas_access',
                'belanja_access',
                'hutang_access',
            ],
            'inventory' => [
                'produk_access',
                'pemakaian_access',
                'opname_access',
                'po_access',
            ],
            'produksi' => [
                'produksi_proses_access',
                'produksi_produk_access',
            ],
            'pegawai' => [
                'member_access',
                'freelance_access',
                'absensi_access',
                'ar_access',
            ],
            'analisa' => [
                'analisa_beban_access',
                'analisa_operasional_access',
                'analisa_stok_access',
            ],
            'laporan' => [
                'laporan_tunjangan_access',
                'laporan_penggajian_access',
                'laporan_neraca_access',
                'laporan_labarugi_access',
            ],
            'omzet' => [
                'omzet_tahunan_access',
                'omzet_bulanan_access',
                'omzet_marketplace_access',
                'omzet_aset_access',
                'omzet_produk_access',
            ],
            'user_mgmt' => [
                'user_access',
                'level_access',
                'bagian_access',
            ],
            'config' => [
                self::RBAC_MANAGE,
                'setup_produksi_access',
                'spek_access',
                'pemproses_access',
                'sistem_access',
                'link_page_access',
            ],
        ];
    }

    /**
     * Alias permission lama → permission baru (kompatibilitas Gate/@can di kode existing).
     *
     * @return array<string, list<string>>
     */
    public static function legacyAliases(): array
    {
        return [
            'order_access' => [
                'order_proses_access',
                'order_offline_access',
                'order_online_access',
            ],
            'order_create' => ['order_proses_create'],
            'order_edit' => ['order_proses_edit'],
            'order_delete' => ['order_proses_delete'],
            'marketplace_access' => [
                'mp_custom_access',
                'mp_packing_access',
                'mp_arsip_access',
            ],
            'marketplace_config' => [
                'mp_produk_access',
                'mp_config_access',
                'mp_sync_access',
                'mp_analisa_access',
            ],
            'marketplace_create' => ['mp_config_create'],
            'marketplace_edit' => ['mp_config_edit'],
            'marketplace_delete' => ['mp_config_delete'],
            'akun_detail_access' => ['kas_access'],
            'akun_detail_create' => ['kas_create'],
            'akun_detail_edit' => ['kas_edit'],
            'akun_detail_delete' => ['kas_delete'],
            'keuangan' => ['belanja_access', 'hutang_access', 'belum_lunas_access'],
            'pakaiStok_create' => ['pemakaian_create'],
            'pakaiStok_Edit' => ['pemakaian_edit'],
            'pakaiStok_access' => ['pemakaian_access'],
            'pakaiStok_delete' => ['pemakaian_delete'],
            'laporan_access' => [
                'analisa_beban_access',
                'analisa_operasional_access',
                'analisa_stok_access',
                'laporan_tunjangan_access',
                'laporan_penggajian_access',
                'laporan_neraca_access',
                'laporan_labarugi_access',
            ],
            'omzet_access' => [
                'omzet_tahunan_access',
                'omzet_bulanan_access',
                'omzet_marketplace_access',
                'omzet_aset_access',
                'omzet_produk_access',
            ],
            'gaji_show' => ['gaji_access'],
            'proce_access' => ['pemproses_access'],
            'proce_create' => ['pemproses_create'],
            'proce_edit' => ['pemproses_edit'],
            'proce_delete' => ['pemproses_delete'],
            'proce_show' => ['pemproses_access'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function actionLabels(): array
    {
        return [
            'read' => 'Read',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
        ];
    }

    /**
     * @return array<string, array{label: string, group?: string, permissions: array<string, string>}>
     */
    public static function catalog(): array
    {
        $catalog = [];

        foreach (self::menus() as $key => $menu) {
            $permissions = [];

            foreach (self::CRUD_ACTIONS as $action) {
                $name = $menu['actions'][$action] ?? null;
                if ($name) {
                    $permissions[$name] = self::actionLabels()[$action];
                }
            }

            foreach ($menu['extras'] ?? [] as $extraKey => $name) {
                $permissions[$name] = ucfirst($extraKey);
            }

            $catalog[$key] = [
                'label' => $menu['label'],
                'group' => $menu['group'] ?? 'Lainnya',
                'permissions' => $permissions,
            ];
        }

        return $catalog;
    }

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        $names = [];

        foreach (self::menus() as $menu) {
            foreach (self::CRUD_ACTIONS as $action) {
                $name = $menu['actions'][$action] ?? null;
                if ($name) {
                    $names[] = $name;
                }
            }
            foreach ($menu['extras'] ?? [] as $name) {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    public static function exists(string $name): bool
    {
        return in_array($name, self::all(), true);
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        $labels = [];

        foreach (self::catalog() as $group) {
            foreach ($group['permissions'] as $name => $label) {
                $labels[$name] = $label;
            }
        }

        return $labels;
    }

    /**
     * @return list<string>
     */
    public static function namesForMenu(string $menuKey): array
    {
        $menu = self::menus()[$menuKey] ?? null;
        if (! $menu) {
            return [];
        }

        $names = [];
        foreach (self::CRUD_ACTIONS as $action) {
            $name = $menu['actions'][$action] ?? null;
            if ($name) {
                $names[] = $name;
            }
        }
        foreach ($menu['extras'] ?? [] as $name) {
            $names[] = $name;
        }

        return $names;
    }
}

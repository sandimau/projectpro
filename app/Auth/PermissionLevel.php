<?php

namespace App\Auth;

class PermissionLevel
{
    public const NONE = '';
    public const R = 'R';
    public const RC = 'RC';
    public const RCE = 'RCE';
    public const ALL = 'ALL';

    /**
     * Urutan siklus klik sel.
     *
     * @var list<string>
     */
    public const CYCLE = [
        self::NONE,
        self::R,
        self::RC,
        self::RCE,
        self::ALL,
    ];

    /**
     * Aksi CRUD yang diaktifkan per level.
     *
     * @return array<string, list<string>>
     */
    public static function actionMap(): array
    {
        return [
            self::NONE => [],
            self::R => ['read'],
            self::RC => ['read', 'create'],
            self::RCE => ['read', 'create', 'update'],
            self::ALL => ['read', 'create', 'update', 'delete'],
        ];
    }

    /**
     * @return array<string, array{label: string, class: string, title: string}>
     */
    public static function meta(): array
    {
        return [
            self::NONE => [
                'label' => '—',
                'class' => 'perm-badge perm-none',
                'title' => 'Tidak ada akses',
            ],
            self::R => [
                'label' => 'R',
                'class' => 'perm-badge perm-r',
                'title' => 'Read',
            ],
            self::RC => [
                'label' => 'RC',
                'class' => 'perm-badge perm-rc',
                'title' => 'Read + Create',
            ],
            self::RCE => [
                'label' => 'RCE',
                'class' => 'perm-badge perm-rce',
                'title' => 'Read + Create + Edit',
            ],
            self::ALL => [
                'label' => 'ALL',
                'class' => 'perm-badge perm-all',
                'title' => 'Read + Create + Edit + Delete',
            ],
        ];
    }

    /**
     * Level yang masuk akal untuk menu (berdasarkan aksi yang tersedia).
     *
     * @param  array{actions: array<string, ?string>, extras?: array<string, string>}  $menu
     * @return list<string>
     */
    public static function availableForMenu(array $menu): array
    {
        $hasRead = ! empty($menu['actions']['read'] ?? null);
        $hasCreate = ! empty($menu['actions']['create'] ?? null);
        $hasUpdate = ! empty($menu['actions']['update'] ?? null);
        $hasDelete = ! empty($menu['actions']['delete'] ?? null);
        $hasExtras = ! empty($menu['extras']);
        $hasAny = $hasRead || $hasCreate || $hasUpdate || $hasDelete || $hasExtras;

        $levels = [self::NONE];

        if ($hasRead) {
            $levels[] = self::R;
        }
        if ($hasRead && $hasCreate) {
            $levels[] = self::RC;
        }
        if ($hasRead && $hasCreate && $hasUpdate) {
            $levels[] = self::RCE;
        }
        if ($hasAny) {
            $levels[] = self::ALL;
        }

        // Hindari duplikat visual: jika ALL == R (hanya read), buang ALL
        if ($hasRead && ! $hasCreate && ! $hasUpdate && ! $hasDelete && ! $hasExtras) {
            $levels = [self::NONE, self::R];
        }

        return array_values(array_unique($levels));
    }

    /**
     * Deteksi level dari daftar permission yang dimiliki role.
     *
     * @param  array{actions: array<string, ?string>, extras?: array<string, string>}  $menu
     * @param  list<string>  $owned
     */
    public static function detect(array $menu, array $owned): string
    {
        $ownedLookup = array_fill_keys($owned, true);
        $available = array_filter([
            'read' => $menu['actions']['read'] ?? null,
            'create' => $menu['actions']['create'] ?? null,
            'update' => $menu['actions']['update'] ?? null,
            'delete' => $menu['actions']['delete'] ?? null,
        ]);

        if ($available === []) {
            // hanya extras
            $extras = $menu['extras'] ?? [];
            if ($extras === []) {
                return self::NONE;
            }
            $allExtras = true;
            foreach ($extras as $name) {
                if (! isset($ownedLookup[$name])) {
                    $allExtras = false;
                    break;
                }
            }

            return $allExtras ? self::ALL : self::NONE;
        }

        $has = [];
        foreach ($available as $action => $name) {
            $has[$action] = isset($ownedLookup[$name]);
        }

        $wantDelete = ! empty($available['delete']);
        $wantUpdate = ! empty($available['update']);
        $wantCreate = ! empty($available['create']);
        $wantRead = ! empty($available['read']);

        if ($wantDelete && ($has['delete'] ?? false)
            && (! $wantUpdate || ($has['update'] ?? false))
            && (! $wantCreate || ($has['create'] ?? false))
            && (! $wantRead || ($has['read'] ?? false))
        ) {
            return self::ALL;
        }

        if ($wantUpdate && ($has['update'] ?? false)
            && (! $wantCreate || ($has['create'] ?? false))
            && (! $wantRead || ($has['read'] ?? false))
        ) {
            // full available tanpa delete → ALL, atau RCE jika delete ada tapi tidak dimiliki
            if (! $wantDelete) {
                return self::ALL;
            }

            return self::RCE;
        }

        if ($wantCreate && ($has['create'] ?? false) && (! $wantRead || ($has['read'] ?? false))) {
            if (! $wantUpdate && ! $wantDelete) {
                return self::ALL;
            }

            return self::RC;
        }

        if ($wantRead && ($has['read'] ?? false)) {
            if (! $wantCreate && ! $wantUpdate && ! $wantDelete) {
                return self::ALL;
            }

            return self::R;
        }

        return self::NONE;
    }

    /**
     * Nama permission Spatie yang harus di-assign untuk level ini pada menu.
     *
     * @param  array{actions: array<string, ?string>, extras?: array<string, string>}  $menu
     * @return list<string>
     */
    public static function permissionNames(array $menu, string $level): array
    {
        if ($level === self::NONE || $level === '-') {
            return [];
        }

        $actions = self::actionMap()[$level] ?? null;
        if ($actions === null) {
            return [];
        }

        // ALL = semua aksi yang tersedia di menu
        if ($level === self::ALL) {
            $names = [];
            foreach (Permissions::CRUD_ACTIONS as $action) {
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

        $names = [];
        foreach ($actions as $action) {
            $name = $menu['actions'][$action] ?? null;
            if ($name) {
                $names[] = $name;
            }
        }

        return $names;
    }

    public static function next(string $current, array $available): string
    {
        if ($available === []) {
            return self::NONE;
        }

        $idx = array_search($current, $available, true);
        if ($idx === false) {
            return $available[0];
        }

        return $available[($idx + 1) % count($available)];
    }
}

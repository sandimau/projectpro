@extends('layouts.app')

@section('title')
    Roles & Akses
@endsection

@push('before-styles')
    <style>
        .perm-matrix-wrap {
            overflow: auto;
            max-height: calc(100vh - 380px);
            border: 1px solid var(--cui-border-color, #d8dbe0);
            border-radius: 0.375rem;
            background: var(--cui-body-bg, #fff);
        }

        .perm-matrix {
            border-collapse: separate;
            border-spacing: 0;
            width: max-content;
            min-width: 100%;
            margin: 0;
        }

        .perm-matrix th,
        .perm-matrix td {
            border-bottom: 1px solid var(--cui-border-color, #e9ecef);
            border-right: 1px solid var(--cui-border-color, #e9ecef);
            padding: 0.45rem 0.55rem;
            white-space: nowrap;
            vertical-align: middle;
        }

        .perm-matrix thead th {
            position: sticky;
            top: 0;
            z-index: 3;
            background: var(--cui-tertiary-bg, #f8f9fa);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: lowercase;
            text-align: center;
            min-width: 88px;
        }

        .perm-matrix .col-menu {
            position: sticky;
            left: 0;
            z-index: 4;
            background: var(--cui-body-bg, #fff);
            text-align: left;
            min-width: 160px;
            max-width: 200px;
            font-weight: 500;
        }

        .perm-matrix thead .col-menu {
            z-index: 5;
            background: var(--cui-tertiary-bg, #f8f9fa);
        }

        .perm-matrix .group-row td {
            background: #ece6f7;
            color: #4a3f6b;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: lowercase;
            letter-spacing: 0.02em;
            position: sticky;
            left: 0;
            z-index: 2;
        }

        [data-theme="dark"] .perm-matrix .group-row td {
            background: #3a3355;
            color: #ddd6fe;
        }

        .perm-matrix tbody tr:nth-child(even):not(.group-row) td {
            background: rgba(0, 0, 0, 0.015);
        }

        .perm-matrix tbody tr:nth-child(even):not(.group-row) td.col-menu {
            background: var(--cui-tertiary-bg, #fafafa);
        }

        .perm-cell {
            text-align: center;
            cursor: pointer;
            user-select: none;
        }

        .perm-cell:hover {
            background: rgba(99, 102, 241, 0.08) !important;
        }

        .perm-badge {
            display: inline-block;
            min-width: 2.6rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            line-height: 1.3;
            text-align: center;
            border: 0;
        }

        .perm-none {
            background: transparent;
            color: #9ca3af;
            font-weight: 600;
            min-width: auto;
            padding: 0.2rem 0.35rem;
        }

        .perm-r {
            background: #2dd4bf;
            color: #064e3b;
        }

        .perm-rc {
            background: #6366f1;
            color: #fff;
        }

        .perm-rce {
            background: #4f46e5;
            color: #fff;
        }

        .perm-all {
            background: #ec4899;
            color: #fff;
        }

        .perm-legend .perm-badge {
            margin-right: 0.25rem;
        }

        .perm-matrix-actions {
            position: sticky;
            bottom: 0;
            background: var(--cui-body-bg, #fff);
            padding-top: 1rem;
            z-index: 6;
        }

        .role-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.5rem;
            border: 1px solid var(--cui-border-color, #d8dbe0);
            border-radius: 0.375rem;
            background: var(--cui-tertiary-bg, #f8f9fa);
            margin: 0.25rem;
        }

        .role-chip form {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            margin: 0;
        }

        .role-chip input[type="text"] {
            width: 110px;
            font-size: 0.8rem;
            padding: 0.15rem 0.35rem;
        }
    </style>
@endpush

@section('content')
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">Roles & Akses</h5>
                <small class="text-muted">Kelola role dan matriks permission dalam satu halaman</small>
            </div>
            <form action="{{ route('permissions.sync') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm"
                    onclick="return confirm('Sinkronkan katalog & hapus orphan?')">
                    Sync Katalog
                </button>
            </form>
        </div>
        <div class="card-body">
            @include('layouts.includes.messages')

            @if (! empty($purged) && $purged > 0)
                <div class="alert alert-info py-2">{{ $purged }} orphan permission otomatis dihapus.</div>
            @endif

            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <strong class="me-1">Roles:</strong>
                @foreach ($roles as $role)
                    <div class="role-chip">
                        <form action="{{ route('roles.update', $role) }}" method="post">
                            @csrf
                            @method('patch')
                            <input type="text" name="name" value="{{ $role->name }}"
                                class="form-control form-control-sm"
                                {{ $role->name === 'super' ? 'readonly' : '' }} required>
                            @if ($role->name !== 'super')
                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Rename">✓</button>
                            @endif
                        </form>
                        @if ($role->name !== 'super')
                            <form action="{{ route('roles.destroy', $role) }}" method="post"
                                onsubmit="return confirm('Hapus role {{ $role->name }}?')">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">×</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>

            <form action="{{ route('roles.store') }}" method="post" class="row g-2 align-items-end mb-1">
                @csrf
                <div class="col-auto">
                    <label class="form-label mb-0 small">Tambah role</label>
                    <input type="text" name="name" class="form-control form-control-sm"
                        placeholder="nama role" value="{{ old('name') }}" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Tambah</button>
                </div>
                @error('name')
                    <div class="col-12"><span class="text-danger small">{{ $message }}</span></div>
                @enderror
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-1">Matriks Akses</h5>
            <div class="text-muted small">
                Klik badge untuk mengubah level:
                <span class="perm-legend ms-1">
                    <span class="perm-badge perm-none">—</span> kosong
                    <span class="perm-badge perm-r">R</span> Read
                    <span class="perm-badge perm-rc">RC</span> Read+Create
                    <span class="perm-badge perm-rce">RCE</span> Read+Create+Edit
                    <span class="perm-badge perm-all">ALL</span> Full CRUD
                </span>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('permissions.matrix.save') }}" id="permMatrixForm">
                @csrf

                <div class="perm-matrix-wrap">
                    <table class="perm-matrix">
                        <thead>
                            <tr>
                                <th class="col-menu">submodul</th>
                                @foreach ($roles as $role)
                                    <th title="{{ $role->name }}">{{ $role->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php $currentGroup = null; @endphp
                            @foreach ($menus as $menuKey => $menu)
                                @if (($menu['group'] ?? '') !== $currentGroup)
                                    @php $currentGroup = $menu['group'] ?? ''; @endphp
                                    <tr class="group-row">
                                        <td class="col-menu" colspan="{{ $roles->count() + 1 }}">
                                            {{ strtolower($currentGroup) }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="col-menu" title="{{ $menuKey }}">
                                        {{ strtolower($menu['label']) }}
                                    </td>
                                    @foreach ($roles as $role)
                                        @php
                                            $level = $matrix[$menuKey][$role->id] ?? '';
                                            $meta = $levelMeta[$level] ?? $levelMeta[''];
                                            $avail = $available[$menuKey] ?? [''];
                                        @endphp
                                        <td class="perm-cell"
                                            data-menu="{{ $menuKey }}"
                                            data-role="{{ $role->id }}"
                                            data-level="{{ $level }}"
                                            data-available='@json($avail)'
                                            title="{{ $meta['title'] }} — klik untuk ubah">
                                            <span class="{{ $meta['class'] }}">{{ $meta['label'] }}</span>
                                            <input type="hidden"
                                                name="matrix[{{ $menuKey }}][{{ $role->id }}]"
                                                value="{{ $level }}">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="perm-matrix-actions d-flex gap-2 align-items-center">
                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                    <span class="text-muted small" id="permDirtyHint" style="display:none">Ada perubahan belum disimpan</span>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        (function() {
            const meta = @json($levelMeta);
            const dirtyHint = document.getElementById('permDirtyHint');

            function renderBadge(cell, level) {
                const info = meta[level] || meta[''];
                const badge = cell.querySelector('span');
                if (badge) {
                    badge.className = info.class;
                    badge.textContent = info.label;
                }
                cell.dataset.level = level;
                cell.title = info.title + ' — klik untuk ubah';
                const input = cell.querySelector('input[type="hidden"]');
                if (input) input.value = level;
            }

            function nextLevel(current, available) {
                if (!available.length) return '';
                const idx = available.indexOf(current);
                if (idx === -1) return available[0];
                return available[(idx + 1) % available.length];
            }

            document.querySelectorAll('.perm-cell').forEach(function(cell) {
                cell.addEventListener('click', function() {
                    let available = [];
                    try {
                        available = JSON.parse(cell.dataset.available || '[]');
                    } catch (e) {
                        available = ['', 'R', 'RC', 'RCE', 'ALL'];
                    }
                    const next = nextLevel(cell.dataset.level || '', available);
                    renderBadge(cell, next);
                    if (dirtyHint) dirtyHint.style.display = '';
                });
            });
        })();
    </script>
@endpush

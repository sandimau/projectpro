{{-- Expected: $menus (Permissions::menus), $rolePermissions (array of names) --}}
@php
    use App\Auth\Permissions;
    $rolePermissions = $rolePermissions ?? old('permission', $rolePermissions ?? []);
    $actionLabels = Permissions::actionLabels();
    $currentGroup = null;
@endphp

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <label class="form-label mb-0">Permission per Menu</label>
        <div class="text-muted small">Centang Create / Read / Update / Delete sesuai kebutuhan tiap menu</div>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="all_permission">
        <label class="form-check-label" for="all_permission">Pilih semua</label>
    </div>
</div>

@error('permission')
    <div class="alert alert-danger py-2">{{ $message }}</div>
@enderror

<div class="table-responsive border rounded">
    <table class="table table-sm table-hover align-middle mb-0 permission-matrix">
        <thead class="table-light">
            <tr>
                <th style="min-width: 180px">Menu</th>
                @foreach ($actionLabels as $action => $label)
                    <th class="text-center" style="width: 90px">{{ $label }}</th>
                @endforeach
                <th class="text-center" style="width: 90px">Extras</th>
                <th class="text-center" style="width: 80px">Semua</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($menus as $menuKey => $menu)
                @if (($menu['group'] ?? '') !== $currentGroup)
                    @php $currentGroup = $menu['group'] ?? ''; @endphp
                    <tr class="table-secondary">
                        <td colspan="7" class="fw-semibold small text-uppercase py-2">
                            {{ $currentGroup }}
                        </td>
                    </tr>
                @endif
                <tr data-menu="{{ $menuKey }}">
                    <td>
                        <strong>{{ $menu['label'] }}</strong>
                    </td>
                    @foreach ($actionLabels as $action => $label)
                        @php $permName = $menu['actions'][$action] ?? null; @endphp
                        <td class="text-center">
                            @if ($permName)
                                <input class="form-check-input permission menu-{{ $menuKey }}"
                                    type="checkbox" name="permission[]" value="{{ $permName }}"
                                    id="perm_{{ $permName }}"
                                    title="{{ $permName }}"
                                    {{ in_array($permName, $rolePermissions, true) ? 'checked' : '' }}>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    @endforeach
                    <td class="text-center">
                        @forelse ($menu['extras'] ?? [] as $extraKey => $permName)
                            <div class="form-check d-inline-block mx-1" title="{{ $permName }}">
                                <input class="form-check-input permission menu-{{ $menuKey }}"
                                    type="checkbox" name="permission[]" value="{{ $permName }}"
                                    id="perm_{{ $permName }}"
                                    {{ in_array($permName, $rolePermissions, true) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="perm_{{ $permName }}">
                                    {{ $extraKey }}
                                </label>
                            </div>
                        @empty
                            <span class="text-muted">—</span>
                        @endforelse
                    </td>
                    <td class="text-center">
                        <input class="form-check-input menu-toggle" type="checkbox"
                            data-menu="{{ $menuKey }}" id="menu_all_{{ $menuKey }}"
                            title="Semua aksi menu ini">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@extends('layouts.app')

@section('title')
    Show Role
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Role: {{ $role->name }}</h5>
                <div>
                    @can('rbac.manage')
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-info btn-sm">Edit</a>
                    @endcan
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                @php
                    use App\Auth\Permissions;
                    $actionLabels = Permissions::actionLabels();
                    $currentGroup = null;
                @endphp

                <div class="table-responsive border rounded">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Menu</th>
                                @foreach ($actionLabels as $label)
                                    <th class="text-center">{{ $label }}</th>
                                @endforeach
                                <th class="text-center">Extras</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($menus as $menu)
                                @if (($menu['group'] ?? '') !== $currentGroup)
                                    @php $currentGroup = $menu['group'] ?? ''; @endphp
                                    <tr class="table-secondary">
                                        <td colspan="6" class="fw-semibold small text-uppercase py-2">
                                            {{ $currentGroup }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>{{ $menu['label'] }}</strong></td>
                                    @foreach ($actionLabels as $action => $label)
                                        @php $permName = $menu['actions'][$action] ?? null; @endphp
                                        <td class="text-center">
                                            @if ($permName && in_array($permName, $rolePermissions, true))
                                                <span class="badge text-bg-success">✓</span>
                                            @elseif ($permName)
                                                <span class="text-muted">—</span>
                                            @else
                                                <span class="text-muted opacity-25">·</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        @foreach ($menu['extras'] ?? [] as $extraKey => $permName)
                                            @if (in_array($permName, $rolePermissions, true))
                                                <span class="badge text-bg-info">{{ $extraKey }}</span>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

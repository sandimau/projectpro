@extends('layouts.app')

@section('title')
    Role List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Roles</h5>
                    <x-crud-create permission="rbac.manage" :url="route('roles.create')" label="Tambah Role" />
                </div>
            </div>
            <div class="card-body">
                @include('layouts.includes.messages')
                <div class="table-responsive">
                    <table class="table table-striped" id="rolesTable">
                        <thead>
                            <tr>
                                <th scope="col">Nama</th>
                                <th scope="col">Permissions</th>
                                <th scope="col" style="width: 220px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>
                                        <strong>{{ $role->name }}</strong>
                                        <div class="text-muted small">{{ $role->permissions->count() }} permission</div>
                                    </td>
                                    <td>
                                        @forelse ($role->permissions->take(8) as $perm)
                                            <span class="badge text-bg-info me-1 mb-1">{{ $perm->name }}</span>
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                        @if ($role->permissions->count() > 8)
                                            <span class="badge text-bg-secondary">+{{ $role->permissions->count() - 8 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="{{ route('roles.show', $role->id) }}"
                                                class="btn btn-warning btn-sm">Detail</a>
                                            @can('rbac.manage')
                                                <a href="{{ route('roles.edit', $role->id) }}"
                                                    class="btn btn-info btn-sm">Edit</a>
                                                @if ($role->name !== 'super')
                                                    <form action="{{ route('roles.destroy', $role->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" onclick="return confirm('Hapus role ini?')"
                                                            class="btn btn-danger btn-sm">Hapus</button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
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

@push('after-scripts')
    <script>
        new DataTable('#rolesTable', {
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [{ orderable: false, targets: 2 }],
        });
    </script>
@endpush

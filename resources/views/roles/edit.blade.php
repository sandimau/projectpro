@extends('layouts.app')

@section('title')
    Edit Role
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Role</h5>
            </div>
            <div class="card-body">
                @include('layouts.includes.messages')

                <form method="POST" action="{{ route('roles.update', $role->id) }}">
                    @method('patch')
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Role</label>
                        <input value="{{ old('name', $role->name) }}" type="text"
                            class="form-control @error('name') is-invalid @enderror" name="name"
                            id="name" required {{ $role->name === 'super' ? 'readonly' : '' }}>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('roles._permission-groups', [
                        'menus' => $menus,
                        'rolePermissions' => old('permission', $rolePermissions),
                    ])

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        $(document).ready(function() {
            function syncMenuToggle(menu) {
                const boxes = $('.menu-' + menu);
                const checked = boxes.filter(':checked').length;
                $('#menu_all_' + menu).prop('checked', boxes.length > 0 && checked === boxes.length);
            }

            $('.menu-toggle').each(function() {
                syncMenuToggle($(this).data('menu'));
            });

            $('#all_permission').on('change', function() {
                const on = $(this).is(':checked');
                $('.permission').prop('checked', on);
                $('.menu-toggle').prop('checked', on);
            });

            $('.menu-toggle').on('change', function() {
                $('.menu-' + $(this).data('menu')).prop('checked', $(this).is(':checked'));
            });

            $('.permission').on('change', function() {
                const menu = $(this).attr('class').match(/menu-([\w_]+)/);
                if (menu) syncMenuToggle(menu[1]);
            });
        });
    </script>
@endpush

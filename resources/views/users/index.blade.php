@extends('layouts.app')

@section('title')
    User List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Users</h5>
                    </div>
                    <x-crud-create permission="user_create" :url="route('users.create')" label="Add user" />
                </div>
            </div>
            <div class="card-body">
                @include('layouts.includes.messages')
                <div class="table-responsive">
                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Roles</th>
                                @canany(['user_edit', 'user_delete'])
                                    <th scope="col">Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @forelse ($user->roles as $role)
                                            <span class="badge bg-primary">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </td>
                                    @canany(['user_edit', 'user_delete'])
                                        <td>
                                            <x-crud-actions
                                                edit="user_edit"
                                                delete="user_delete"
                                                :edit-url="route('users.edit', $user->id)"
                                                :delete-url="route('users.destroy', $user->id)"
                                                confirm="Are you sure?"
                                            />
                                        </td>
                                    @endcanany
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
        new DataTable('#myTable', {
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: -1 },
            ],
        });
    </script>
@endpush

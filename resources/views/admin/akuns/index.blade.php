@extends('layouts.app')

@section('title')
    Akun List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Akuns</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your akuns here.</h6>
                    </div>
                    @can('akun_create')
                        <a href="{{ route('akuns.create') }}" class="btn btn-primary ">Add akun</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama</th>
                                <th scope="col">actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($akuns as $akun)
                                <tr>
                                    <td>{{ $akun->id }}</td>
                                    <td>{{ $akun->nama }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('akuns.edit', $akun->id) }}" class="btn btn-info btn-sm me-1"><i
                                                    class='bx bxs-edit'></i> Edit</a>
                                            <form action="{{ route('akuns.destroy', $akun->id) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" onclick="return confirm('Are you sure?')"
                                                    class="btn btn-danger btn-sm"><i class='bx bxs-trash' ></i> delete</button>
                                            </form>
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
        let table = new DataTable('#myTable');
    </script>
@endpush

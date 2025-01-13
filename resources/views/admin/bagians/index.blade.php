@extends('layouts.app')

@section('title')
    Bagian List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Bagian</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your bagian here.</h6>
                    </div>
                    @can('level_create')
                        <a href="{{ route('bagian.create') }}" class="btn btn-primary ">Add bagian</a>
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
                                <th scope="col">nama</th>
                                <th scope="col">grade</th>
                                <th scope="col">action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bagians as $bagian)
                                <tr>
                                    <td>{{ $bagian->nama }}</td>
                                    <td>{{ $bagian->grade }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('bagian.edit', $bagian->id) }}" class="btn btn-info btn-sm me-1"><i
                                                    class='bx bxs-edit'></i> Edit</a>
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

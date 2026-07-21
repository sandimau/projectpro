@extends('layouts.app')

@section('title')
    Company List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Company</h5>
                        <small class="text-muted">Setiap company diakses via subdomain (slug.domain)</small>
                    </div>
                    @can('company_create')
                        <a href="{{ route('companies.create') }}" class="btn btn-primary">Tambah Company</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                @include('layouts.includes.messages')
                <div class="table-responsive">
                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">Nama</th>
                                <th scope="col">Slug / Subdomain</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($companies as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <code>{{ $item->slug }}</code>
                                        @php
                                            $central = config('tenancy.central_domains')[0] ?? 'projectpro.com';
                                        @endphp
                                        <div class="small text-muted">{{ $item->slug }}.{{ $central }}</div>
                                    </td>
                                    <td>
                                        @if ($item->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            @can('company_edit')
                                                <a href="{{ route('companies.edit', $item->id) }}"
                                                    class="btn btn-info btn-sm me-1"><i class='bx bxs-edit'></i>
                                                    Edit</a>
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

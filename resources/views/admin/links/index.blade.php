@extends('layouts.app')

@section('title', 'Link Pages')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Link Pages</strong>
        <a href="{{ route('linkPages.create') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-plus"></i> Tambah Link Page
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover" id="dataTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($linkPages as $index => $page)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($page->logo)
                                <img src="{{ asset('uploads/links/' . $page->logo) }}" alt="Logo" width="30" class="me-2 rounded-circle">
                            @endif
                            {{ $page->title }}
                        </td>
                        <td>
                            <a href="{{ route('link.show', $page->slug) }}" target="_blank" class="text-decoration-none">
                                /link/{{ $page->slug }} <i class="bx bx-link-external"></i>
                            </a>
                        </td>
                        <td><span class="badge bg-info">{{ $page->items_count }} items</span></td>
                        <td>
                            @if($page->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('linkPages.items', $page) }}" class="btn btn-info btn-sm" title="Kelola Items">
                                    <i class="bx bx-list-ul"></i>
                                </a>
                                <a href="{{ route('linkPages.show', $page) }}" class="btn btn-secondary btn-sm" title="Preview">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="{{ route('linkPages.edit', $page) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $page->id }}" title="Hapus">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $page->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Yakin ingin menghapus <strong>{{ $page->title }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('linkPages.destroy', $page) }}" method="POST" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endpush


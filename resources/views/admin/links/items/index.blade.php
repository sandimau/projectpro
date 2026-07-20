@extends('layouts.app')

@section('title', 'Kelola Items: ' . $linkPage->title)

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Kelola Items: {{ $linkPage->title }}</strong>
        <div>
            <a href="{{ route('linkPages.items.create', $linkPage) }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> Tambah Item
            </a>
            <a href="{{ route('linkPages.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
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
                        <th>Order</th>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Section</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->order }}</td>
                        <td>
                            @if($item->icon)
                                <img src="{{ asset('uploads/links/icons/' . $item->icon) }}" alt="Icon" width="30" class="rounded">
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $item->title }}</td>
                        <td>
                            @if($item->type == 'social')
                                <span class="badge bg-info">Social</span>
                            @else
                                <span class="badge bg-primary">Link</span>
                            @endif
                        </td>
                        <td>{{ $item->section ?: '-' }}</td>
                        <td>
                            <a href="{{ $item->url }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
                                {{ $item->url }}
                            </a>
                        </td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('linkPages.items.edit', [$linkPage, $item]) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}" title="Hapus">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Yakin ingin menghapus <strong>{{ $item->title }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('linkPages.items.destroy', [$linkPage, $item]) }}" method="POST" style="display:inline">
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
        $('#dataTable').DataTable({
            order: [[0, 'asc']]
        });
    });
</script>
@endpush


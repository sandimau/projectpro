@extends('layouts.app')

@section('title', 'Tambah Item: ' . $linkPage->title)

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Tambah Item untuk: {{ $linkPage->title }}</strong>
        <a href="{{ route('linkPages.items', $linkPage) }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('linkPages.items.store', $linkPage) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="link" {{ old('type') == 'link' ? 'selected' : '' }}>Link</option>
                            <option value="social" {{ old('type') == 'social' ? 'selected' : '' }}>Social Media</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Social Media akan ditampilkan sebagai icon di bagian atas, Link akan ditampilkan sebagai tombol</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="order" class="form-label">Urutan</label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror"
                               id="order" name="order" value="{{ old('order', 0) }}">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror"
                       id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="url" class="form-label">URL <span class="text-danger">*</span></label>
                <input type="url" class="form-control @error('url') is-invalid @enderror"
                       id="url" name="url" value="{{ old('url') }}" placeholder="https://" required>
                @error('url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" id="sectionField">
                <label for="section" class="form-label">Section / Kategori</label>
                <input type="text" class="form-control @error('section') is-invalid @enderror"
                       id="section" name="section" value="{{ old('section') }}"
                       placeholder="contoh: Official Online Store, Belanja dengan Cititex">
                @error('section')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Kosongkan jika tidak ingin dikelompokkan. Link dengan section yang sama akan ditampilkan dalam satu grup.</small>
            </div>

            <div class="mb-3">
                <label for="icon" class="form-label">Icon/Gambar</label>
                <input type="file" class="form-control @error('icon') is-invalid @enderror"
                       id="icon" name="icon" accept="image/*">
                @error('icon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i> Simpan
            </button>
        </form>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    document.getElementById('type').addEventListener('change', function() {
        const sectionField = document.getElementById('sectionField');
        if (this.value === 'social') {
            sectionField.style.display = 'none';
        } else {
            sectionField.style.display = 'block';
        }
    });
    // Trigger on load
    document.getElementById('type').dispatchEvent(new Event('change'));
</script>
@endpush


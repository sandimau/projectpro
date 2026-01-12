@extends('layouts.app')

@section('title', 'Tambah Link Page')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Tambah Link Page</strong>
        <a href="{{ route('linkPages.index') }}" class="btn btn-secondary btn-sm">
            <i class="bx bx-arrow-back"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('linkPages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">/link/</span>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                   id="slug" name="slug" value="{{ old('slug') }}" required>
                        </div>
                        @error('slug')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="logo" class="form-label">Logo</label>
                <input type="file" class="form-control @error('logo') is-invalid @enderror"
                       id="logo" name="logo" accept="image/*">
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="background_color" class="form-label">Background Color</label>
                        <input type="color" class="form-control form-control-color w-100"
                               id="background_color" name="background_color" value="{{ old('background_color', '#000000') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="text_color" class="form-label">Text Color</label>
                        <input type="color" class="form-control form-control-color w-100"
                               id="text_color" name="text_color" value="{{ old('text_color', '#ffffff') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="button_color" class="form-label">Button Color</label>
                        <input type="color" class="form-control form-control-color w-100"
                               id="button_color" name="button_color" value="{{ old('button_color', '#ffffff') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="button_text_color" class="form-label">Button Text Color</label>
                        <input type="color" class="form-control form-control-color w-100"
                               id="button_text_color" name="button_text_color" value="{{ old('button_text_color', '#000000') }}">
                    </div>
                </div>
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
    document.getElementById('title').addEventListener('input', function() {
        let slug = this.value.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        document.getElementById('slug').value = slug;
    });
</script>
@endpush


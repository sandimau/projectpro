@extends('layouts.app')

@section('title')
    add gambar
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page"> <a
                    href="{{ route('projectmp.detail', $detail->ProjectMp->id) }}">{{ $detail->ProjectMp->konsumen }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Gambar</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('projectMpDetail.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="ProjectMp_detail_id" value="{{ $detail->id }}">
                <div class="mb-3">
                    <label for="formFile" class="form-label">gambar</label>
                    <input class="form-control" type="file" id="formFile" name="gambar">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary mt-4" type="submit">
                        save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

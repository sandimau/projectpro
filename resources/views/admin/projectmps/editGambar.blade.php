@extends('layouts.app')

@section('title')
    edit gambar
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">edit gambar</h5>
                </div>
                <a href="{{ route('projectmp.detail', $detail->ProjectMp->id) }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('projectMpDetail.updateGambar', $detail->id) }}" enctype="multipart/form-data">
                @method('patch')
                @csrf
                <input type="hidden" name="ProjectMp_detail_id" value="{{ $detail->id }}">
                <div class="mb-3">
                    <label for="formFile" class="form-label">gambar</label>
                    <input class="form-control" type="file" id="formFile" name="gambar">
                </div>
                <img style="height: 300px; cursor: pointer" src="{{ asset('uploads/projectMp/' . $detail->gambar) }}">
                <div class="form-group">
                    <button class="btn btn-primary mt-4" type="submit">
                        save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title')
    Edit ProjectMP Detail
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page"> <a
                href="{{ route('projectmp.detail', $detail->projectMp->id) }}">ProjectDetail</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit ProjectDetail</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('projectMpDetail.update', $detail->id) }}" enctype="multipart/form-data">
                @method('patch')
                @csrf
                <div class="form-group mb-3">
                    <label for="deadline">Deathline</label>
                    <input class="form-control {{ $errors->has('deadline') ? 'is-invalid' : '' }}" type="date"
                        name="deadline" id="deadline" value="{{ old('deadline', \Carbon\Carbon::parse($detail->deadline)->format('Y-m-d')) }}">
                    @if ($errors->has('deadline'))
                        <div class="invalid-feedback">
                            {{ $errors->first('deadline') }}
                        </div>
                    @endif
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

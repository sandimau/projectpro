@extends('layouts.app')

@section('title', 'Preview: ' . $linkPage->title)

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Preview: {{ $linkPage->title }}</strong>
        <div>
            <a href="{{ route('link.show', $linkPage->slug) }}" target="_blank" class="btn btn-info btn-sm">
                <i class="bx bx-link-external"></i> Buka di Tab Baru
            </a>
            <a href="{{ route('linkPages.index') }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <!-- Preview Phone Frame -->
                <div class="phone-frame mx-auto" style="max-width: 320px; border: 12px solid #333; border-radius: 36px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <iframe src="{{ route('link.show', $linkPage->slug) }}"
                            style="width: 100%; height: 580px; border: none;"></iframe>
                </div>
            </div>
            <div class="col-md-8">
                <h5>Informasi Link Page</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Title</th>
                        <td>{{ $linkPage->title }}</td>
                    </tr>
                    <tr>
                        <th>Slug / URL</th>
                        <td>
                            <code>{{ url('/link/' . $linkPage->slug) }}</code>
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($linkPage->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Background Color</th>
                        <td>
                            <span class="badge" style="background-color: {{ $linkPage->background_color }}; color: {{ $linkPage->text_color }}">
                                {{ $linkPage->background_color }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Text Color</th>
                        <td>{{ $linkPage->text_color }}</td>
                    </tr>
                    <tr>
                        <th>Button Color</th>
                        <td>
                            <span class="badge" style="background-color: {{ $linkPage->button_color }}; color: {{ $linkPage->button_text_color }}">
                                {{ $linkPage->button_color }}
                            </span>
                        </td>
                    </tr>
                </table>

                <h5 class="mt-4">Social Links ({{ $linkPage->socialLinks->count() }})</h5>
                @if($linkPage->socialLinks->count() > 0)
                    <ul class="list-group mb-3">
                        @foreach($linkPage->socialLinks as $social)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $social->title }}
                                <a href="{{ $social->url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-link-external"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Belum ada social links</p>
                @endif

                <h5 class="mt-4">Link Items</h5>
                @if($sections->count() > 0)
                    @foreach($sections as $sectionName => $items)
                        <h6 class="mt-3">{{ $sectionName ?: 'Tanpa Section' }}</h6>
                        <ul class="list-group mb-2">
                            @foreach($items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $item->title }}
                                    <a href="{{ $item->url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-link-external"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                @else
                    <p class="text-muted">Belum ada link items</p>
                @endif

                <div class="mt-4">
                    <a href="{{ route('linkPages.items', $linkPage) }}" class="btn btn-primary">
                        <i class="bx bx-list-ul"></i> Kelola Items
                    </a>
                    <a href="{{ route('linkPages.edit', $linkPage) }}" class="btn btn-warning">
                        <i class="bx bx-edit"></i> Edit Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


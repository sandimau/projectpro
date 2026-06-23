@extends('layouts.app')

@section('title')
    edit gambar
@endsection

@section('content')
    <div class="card">
        <form id="editGambarForm" method="POST" action="{{ route('orderDetail.updateGambar', $detail->id) }}"
            enctype="multipart/form-data" class="order-detail-ajax-form"
            data-reload-detail="{{ route('order.detail', $detail->order->id) }}">
            @method('patch')
            @csrf
            <input type="hidden" name="order_detail_id" value="{{ $detail->id }}">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" type="submit">save</button>
                </div>
                <div class="mb-3">
                    <label for="formFile" class="form-label">gambar</label>
                    <input class="form-control {{ $errors->has('gambar') ? 'is-invalid' : '' }}" type="file"
                        id="formFile" name="gambar" accept="image/jpeg,image/png,image/jpg">
                    @if ($errors->has('gambar'))
                        <div class="invalid-feedback d-block">{{ $errors->first('gambar') }}</div>
                    @endif
                </div>
                <div class="edit-gambar-zoom" id="editGambarZoom">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Kontrol zoom">
                            <button type="button" class="btn btn-outline-secondary" data-zoom-out title="Perkecil">
                                <i class='bx bx-zoom-out'></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary px-3" data-zoom-level disabled>100%</button>
                            <button type="button" class="btn btn-outline-secondary" data-zoom-in title="Perbesar">
                                <i class='bx bx-zoom-in'></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-zoom-reset title="Reset zoom">
                                Reset
                            </button>
                        </div>
                        <small class="text-muted">Scroll mouse atau klik gambar untuk zoom</small>
                    </div>
                    <div class="edit-gambar-zoom-viewport" data-zoom-viewport tabindex="0">
                        <img class="edit-gambar-zoom-img" data-zoom-img
                            src="{{ asset('uploads/order/' . $detail->gambar) }}" alt="Gambar saat ini">
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('after-styles')
    <style>
        .edit-gambar-zoom-viewport {
            overflow: auto;
            max-height: 70vh;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            background: #f8f9fa;
            padding: 0.75rem;
            text-align: center;
            cursor: zoom-in;
        }

        .edit-gambar-zoom-viewport.is-zoomed {
            cursor: grab;
        }

        .edit-gambar-zoom-viewport.is-dragging {
            cursor: grabbing;
        }

        .edit-gambar-zoom-img {
            display: inline-block;
            max-width: 100%;
            max-height: 600px;
            width: auto;
            height: auto;
            user-select: none;
            -webkit-user-drag: none;
        }
    </style>
@endpush

@push('after-scripts')
    <script>
        (function() {
            const root = document.getElementById('editGambarZoom');
            if (!root) return;

            const viewport = root.querySelector('[data-zoom-viewport]');
            const img = root.querySelector('[data-zoom-img]');
            const levelBtn = root.querySelector('[data-zoom-level]');
            const fileInput = document.getElementById('formFile');

            const minScale = 0.5;
            const maxScale = 4;
            const step = 0.25;
            let scale = 1;
            let baseWidth = 0;
            let previewUrl = null;

            function updateLevelLabel() {
                levelBtn.textContent = Math.round(scale * 100) + '%';
            }

            function captureBaseWidth() {
                img.style.width = '';
                img.style.maxHeight = '600px';
                baseWidth = img.offsetWidth || img.naturalWidth || 0;
            }

            function applyZoom() {
                if (!baseWidth) {
                    captureBaseWidth();
                }

                if (!baseWidth) return;

                img.style.maxHeight = 'none';
                img.style.width = Math.round(baseWidth * scale) + 'px';
                viewport.classList.toggle('is-zoomed', scale > 1);
                updateLevelLabel();
            }

            function setScale(nextScale) {
                scale = Math.min(maxScale, Math.max(minScale, nextScale));
                applyZoom();
            }

            function zoomIn() {
                setScale(scale + step);
            }

            function zoomOut() {
                setScale(scale - step);
            }

            function resetZoom() {
                scale = 1;
                applyZoom();
            }

            img.addEventListener('load', function() {
                baseWidth = 0;
                captureBaseWidth();
                applyZoom();
            });

            if (img.complete) {
                captureBaseWidth();
                applyZoom();
            }

            root.querySelector('[data-zoom-in]').addEventListener('click', zoomIn);
            root.querySelector('[data-zoom-out]').addEventListener('click', zoomOut);
            root.querySelector('[data-zoom-reset]').addEventListener('click', resetZoom);

            viewport.addEventListener('wheel', function(e) {
                e.preventDefault();
                if (e.deltaY < 0) {
                    zoomIn();
                } else {
                    zoomOut();
                }
            }, { passive: false });

            img.addEventListener('click', function() {
                if (scale < 2) {
                    setScale(2);
                } else {
                    resetZoom();
                }
            });

            let dragActive = false;
            let dragStartX = 0;
            let dragStartY = 0;
            let scrollStartX = 0;
            let scrollStartY = 0;

            viewport.addEventListener('mousedown', function(e) {
                if (scale <= 1 || e.button !== 0) return;

                dragActive = true;
                dragStartX = e.clientX;
                dragStartY = e.clientY;
                scrollStartX = viewport.scrollLeft;
                scrollStartY = viewport.scrollTop;
                viewport.classList.add('is-dragging');
            });

            window.addEventListener('mousemove', function(e) {
                if (!dragActive) return;

                viewport.scrollLeft = scrollStartX - (e.clientX - dragStartX);
                viewport.scrollTop = scrollStartY - (e.clientY - dragStartY);
            });

            window.addEventListener('mouseup', function() {
                dragActive = false;
                viewport.classList.remove('is-dragging');
            });

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (!file) return;

                    if (previewUrl) {
                        URL.revokeObjectURL(previewUrl);
                    }

                    previewUrl = URL.createObjectURL(file);
                    baseWidth = 0;
                    scale = 1;
                    img.src = previewUrl;
                });
            }
        })();
    </script>
@endpush

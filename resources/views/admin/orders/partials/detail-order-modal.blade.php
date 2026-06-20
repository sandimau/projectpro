<!-- Modal Detail Order -->
<link rel="stylesheet" href="{{ asset('js/autocomplete.css') }}">
<div class="modal fade" id="detailOrderModal" tabindex="-1" aria-labelledby="detailOrderModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-lg-down modal-dialog-scrollable modal-dialog-centered modal-xxl">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title flex-grow-1 mb-0" id="detailOrderModalLabel">Detail Order</h5>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn btn-warning btn-sm text-white" id="detailOrderModalBack"
                        style="display: none;" aria-label="Kembali">
                        <i class='bx bx-arrow-back'></i> back
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body" id="detailOrderBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/autocomplete.min.js') }}"></script>

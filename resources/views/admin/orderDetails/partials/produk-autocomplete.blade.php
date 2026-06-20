<div class="order-detail-produk-autocomplete autocomplete">
    <div class="order-detail-autocomplete-field">
        <input class="autocomplete-input {{ $errors->has('produk_id') ? 'invalid' : '' }}"
            placeholder="cari produk" aria-label="cari produk"
            value="{{ $produkLabel ?? '' }}">
        <span class="order-detail-autocomplete-clear">
            @if (!empty($produkId))
                <button type="button" class="btn btn-warning btn-sm text-white order-detail-clear-produk">
                    <i class="bx bx-x-circle"></i>
                </button>
            @endif
        </span>
    </div>
    <ul class="autocomplete-result-list"></ul>
    <input type="hidden" class="order-detail-produk-id" name="produk_id" value="{{ $produkId ?? '' }}">
</div>

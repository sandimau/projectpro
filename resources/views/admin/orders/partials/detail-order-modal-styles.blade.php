@media (min-width: 992px) {
    .modal-xxl {
        max-width: 96%;
    }

    .modal-xxl.modal-dialog-scrollable {
        height: calc(100% - 2rem);
    }
}

a.popup {
    text-decoration: none;
}

a.popup:hover {
    text-decoration: underline;
}

#detailOrderModalBack {
    flex-shrink: 0;
}

#detailOrderModal .modal-header .btn-close {
    margin: 0;
}

#detailOrderModal .order-detail-produk-autocomplete {
    max-width: 600px;
    position: relative;
}

#detailOrderModal .order-detail-autocomplete-field {
    position: relative;
}

#detailOrderModal .order-detail-autocomplete-clear {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 4;
    line-height: 1;
}

#detailOrderModal .order-detail-autocomplete-clear:empty {
    display: none;
}

#detailOrderModal .order-detail-produk-autocomplete .autocomplete-input {
    width: 100% !important;
    margin-right: 0 !important;
    padding-right: 42px !important;
    box-sizing: border-box;
}

#detailOrderModal .order-detail-produk-autocomplete .autocomplete-result-list {
    z-index: 1070;
}

#detailOrderModal .order-detail-produk-autocomplete[data-loading="true"]:after {
    right: 42px;
}

#detailOrderModal .order-detail-produk-autocomplete .autocomplete-input.is-invalid,
#detailOrderModal .order-detail-produk-autocomplete .autocomplete-input.invalid {
    border: solid 1px red;
}

#detailOrderModal input[type="hidden"][name="nota"] {
    display: none !important;
}

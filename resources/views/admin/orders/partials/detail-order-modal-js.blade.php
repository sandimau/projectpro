(function() {
    const modalEl = document.getElementById('detailOrderModal');
    if (!modalEl) return;

    const modalBody = document.getElementById('detailOrderBody');
    const modalTitle = document.getElementById('detailOrderModalLabel');
    const bsModal = new bootstrap.Modal(modalEl);
    let modalWasOpened = false;

    const spinner = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>`;

    function extractContent(html) {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const content = doc.querySelector('.body .container-fluid .mb-4') ||
            doc.querySelector('.body .container-fluid') ||
            doc.querySelector('.body');

        return content ? content.innerHTML : html;
    }

    function showModalAlert(message, type) {
        let alert = modalBody.querySelector('.modal-ajax-alert');
        if (!alert) {
            alert = document.createElement('div');
            modalBody.prepend(alert);
        }
        alert.className = 'modal-ajax-alert alert alert-' + type + ' mb-3';
        alert.textContent = message;
        setTimeout(function() {
            alert.remove();
        }, 3000);
    }

    function bindModalForms() {
        modalBody.querySelectorAll('form.order-detail-ajax-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = form.querySelector('[type="submit"]');
                if (submitBtn) submitBtn.disabled = true;

                fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(res) {
                        if (!res.ok) throw new Error('Gagal menyimpan (' + res.status + ')');
                        return res.json();
                    })
                    .then(function(data) {
                        showModalAlert(data.message || 'Berhasil disimpan', 'success');
                    })
                    .catch(function(err) {
                        showModalAlert(err.message, 'danger');
                    })
                    .finally(function() {
                        if (submitBtn) submitBtn.disabled = false;
                    });
            });
        });
    }

    function loadDetailInModal(url) {
        modalBody.innerHTML = spinner;
        modalTitle.textContent = 'Detail Order';
        modalWasOpened = true;
        bsModal.show();

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(res) {
                if (!res.ok) throw new Error('Gagal memuat (' + res.status + ')');
                return res.text();
            })
            .then(function(html) {
                modalBody.innerHTML = extractContent(html);
                bindModalForms();
            })
            .catch(function(err) {
                modalBody.innerHTML =
                    '<div class="alert alert-danger">' + err.message + '</div>';
            });
    }

    document.addEventListener('click', function(e) {
        const link = e.target.closest('a.popup');
        if (!link) return;

        e.preventDefault();
        loadDetailInModal(link.getAttribute('href'));
    });

    modalEl.addEventListener('hidden.bs.modal', function() {
        if (modalWasOpened) {
            location.reload();
        }
    });
})();

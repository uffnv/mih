/**
 * Main JavaScript for "Михаил Потапыч" Store
 */
document.addEventListener('DOMContentLoaded', function() {
    initAddToCart();
    initBackToTop();
    initProductGallery();
    initPasswordToggles();
});

// Toast Notifications System
function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
    }

    const toastEl = document.createElement('div');
    const isSuccess = type === 'success';
    const bgClass = isSuccess ? 'bg-dark text-white' : 'bg-danger text-white';
    const icon = isSuccess ? 'bi-check-circle-fill text-warning' : 'bi-exclamation-triangle-fill text-white';

    toastEl.className = `toast align-items-center ${bgClass} border-0 show custom-toast mb-2`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="bi ${icon} fs-5"></i>
                <span class="fw-medium">${message}</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Закрыть"></button>
        </div>
    `;

    container.appendChild(toastEl);

    // Auto remove after 3.5s
    setTimeout(() => {
        toastEl.classList.remove('show');
        setTimeout(() => toastEl.remove(), 350);
    }, 3500);

    toastEl.querySelector('.btn-close').addEventListener('click', () => toastEl.remove());
}

// Global Add to Cart Handler
function initAddToCart() {
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-add-cart, .add-to-cart');
        if (!btn) return;

        e.preventDefault();
        e.stopImmediatePropagation();

        if (btn.disabled) return;

        const pid = btn.dataset.productId || btn.getAttribute('data-product-id');
        if (!pid) return;

        let qty = 1;
        const wrapper = btn.closest('.product-action-wrapper') || btn.closest('div') || btn.parentElement;
        if (wrapper) {
            const input = wrapper.querySelector('input[name="quantity"], .quantity-input');
            if (input && input.value > 0) qty = parseInt(input.value);
        }

        const originalHtml = btn.innerHTML;
        const originalWidth = btn.offsetWidth;
        btn.style.width = originalWidth ? originalWidth + 'px' : '';
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        const fd = new FormData();
        fd.append('action', 'add');
        fd.append('product_id', pid);
        fd.append('quantity', qty);

        fetch('ajax/cart_action.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            btn.style.width = '';

            if (res.success) {
                // Update badge in header
                const badge = document.getElementById('cart-count-badge');
                if (badge && res.total_qty !== undefined) {
                    badge.innerText = res.total_qty;
                    badge.style.display = res.total_qty > 0 ? 'inline-block' : 'none';
                }

                // Success visual animation on button
                btn.classList.add('btn-success');
                btn.classList.remove('btn-warning');
                const tempText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check2"></i> Добавлено';

                showToast('Товар успешно добавлен в корзину!', 'success');

                setTimeout(() => {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-warning');
                    btn.innerHTML = tempText;
                }, 1500);
            } else {
                showToast(res.message || 'Не удалось добавить товар', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            btn.style.width = '';
            showToast('Ошибка соединения с сервером', 'error');
        });
    });
}

// Back to Top Button
function initBackToTop() {
    let topBtn = document.getElementById('back-to-top');
    if (!topBtn) {
        topBtn = document.createElement('button');
        topBtn.id = 'back-to-top';
        topBtn.className = 'btn btn-warning text-dark';
        topBtn.setAttribute('title', 'Наверх');
        topBtn.innerHTML = '<i class="bi bi-chevron-up fw-bold fs-5"></i>';
        document.body.appendChild(topBtn);
    }

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            topBtn.style.display = 'flex';
        } else {
            topBtn.style.display = 'none';
        }
    });

    topBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// Product Page Gallery
function initProductGallery() {
    const mainImg = document.getElementById('main-product-image');
    if (!mainImg) return;

    document.querySelectorAll('.gallery-thumb').forEach(thumb => {
        thumb.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSrc = this.getAttribute('data-full-img') || this.src;
            mainImg.src = targetSrc;

            document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('border-warning', 'active'));
            this.classList.add('border-warning', 'active');
        });
    });
}

// Password Visibility Toggle
function initPasswordToggles() {
    document.querySelectorAll('.toggle-password-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId) || this.closest('.input-group').querySelector('input');
            if (!input) return;

            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });
}

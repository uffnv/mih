/**
 * Main JavaScript for "Михаил Потапыч" Store
 */
document.addEventListener('DOMContentLoaded', function() {
    initAddToCart();
    initBackToTop();
    initProductGallery();
    initPasswordToggles();
    initAuthFormValidation();
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
                // Update badges in header (desktop + mobile)
                if (res.total_qty !== undefined) {
                    document.querySelectorAll('#cart-count-badge, .cart-count-sync').forEach(badge => {
                        badge.innerText = res.total_qty;
                        badge.style.display = res.total_qty > 0 ? 'inline-block' : 'none';
                    });
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

// Strict Client-Side Input Filtering & Auth Form Validation
function initAuthFormValidation() {
    const forms = document.querySelectorAll('form[data-auth-form]');
    if (!forms.length) return;

    function getErrorContainer(input) {
        const group = input.closest('.mb-3, .mb-4, .col-md-6, .col-12') || input.parentElement;
        return group ? group.querySelector('.field-error-text') : null;
    }

    function setFieldState(input, isValid, message) {
        const errEl = getErrorContainer(input);
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            if (errEl) {
                errEl.textContent = '';
                errEl.classList.remove('show');
            }
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            if (errEl) {
                errEl.textContent = message || 'Проверьте корректность поля.';
                errEl.classList.add('show');
            }
        }
    }

    function clearFieldState(input) {
        input.classList.remove('is-valid', 'is-invalid');
        const errEl = getErrorContainer(input);
        if (errEl) {
            errEl.textContent = '';
            errEl.classList.remove('show');
        }
    }

    function sanitizeAndValidate(input, showEmptyError) {
        const rule = input.dataset.rule;
        let val = input.value;

        if (rule === 'human-name') {
            const filtered = val.replace(/[^А-Яа-яЁёA-Za-z\s-]/g, '').replace(/\s{2,}/g, ' ').replace(/^[\s-]+/, '');
            if (filtered !== val) {
                input.value = filtered;
                val = filtered;
            }
            const trimmed = val.trim();
            if (!trimmed && !showEmptyError) {
                clearFieldState(input);
                return false;
            }
            const ok = trimmed.length >= 2 && trimmed.length <= 50 && /^[А-Яа-яЁёA-Za-z]+([ -][А-Яа-яЁёA-Za-z]+)*$/.test(trimmed);
            setFieldState(input, ok, 'Только буквы (от 2 до 50 символов), пробел или дефис.');
            return ok;
        }

        if (rule === 'login-id') {
            const filtered = val.replace(/[^A-Za-z0-9@._+-]/g, '');
            if (filtered !== val) {
                input.value = filtered;
                val = filtered;
            }
            if (!val && !showEmptyError) {
                clearFieldState(input);
                return false;
            }
            const ok = val.length >= 3 && val.length <= 100 && /^[A-Za-z0-9@._+-]{3,100}$/.test(val);
            setFieldState(input, ok, 'Допустимы только латиница, цифры и @ . _ + - (от 3 символов).');
            return ok;
        }

        if (rule === 'email') {
            const filtered = val.replace(/[^A-Za-z0-9@._%+-]/g, '').toLowerCase();
            if (filtered !== val) {
                input.value = filtered;
                val = filtered;
            }
            if (!val && !showEmptyError) {
                clearFieldState(input);
                return false;
            }
            const ok = val.length <= 100 && /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(val);
            setFieldState(input, ok, 'Введите корректный Email латиницей, например: name@example.ru.');
            return ok;
        }

        if (rule === 'phone') {
            let digits = val.replace(/\D/g, '');
            if (digits.length > 0 && digits[0] === '8') {
                digits = '7' + digits.slice(1);
            } else if (digits.length > 0 && digits[0] !== '7') {
                digits = '7' + digits;
            }
            digits = digits.slice(0, 11);

            let formatted = '';
            if (digits.length > 0) {
                formatted = '+7';
                if (digits.length > 1) formatted += ' (' + digits.slice(1, 4);
                if (digits.length >= 4) formatted += ')';
                if (digits.length > 4) formatted += ' ' + digits.slice(4, 7);
                if (digits.length > 7) formatted += '-' + digits.slice(7, 9);
                if (digits.length > 9) formatted += '-' + digits.slice(9, 11);
            }
            if (val !== '' && formatted !== val && !(typeof IMask !== 'undefined' && input.dataset.imaskBound)) {
                input.value = formatted;
            }

            if (!digits && !showEmptyError) {
                clearFieldState(input);
                return false;
            }
            const allSame = digits.length === 11 && /^(\d)\1{9}$/.test(digits.slice(1));
            const ok = digits.length === 11 && digits[0] === '7' && !allSame;
            setFieldState(input, ok, allSame ? 'Укажите реальный номер телефона.' : 'Введите 11 цифр номера в формате +7 (XXX) XXX-XX-XX.');
            return ok;
        }

        if (rule === 'password-register') {
            const hadBad = /[^\x21-\x7E]/.test(val);
            const filtered = val.replace(/[^\x21-\x7E]/g, '');
            if (filtered !== val) {
                input.value = filtered;
                val = filtered;
            }
            updatePasswordStrength(input, val, hadBad);

            const form = input.closest('form');
            const confirmInput = form ? form.querySelector('[data-rule="password-confirm"]') : null;
            if (confirmInput && confirmInput.value.length > 0) {
                sanitizeAndValidate(confirmInput, true);
            }

            if (!val && !showEmptyError) {
                if (!hadBad) clearFieldState(input);
                return false;
            }
            const ok = val.length >= 6 && val.length <= 64 && /[A-Za-z]/.test(val) && /\d/.test(val);
            setFieldState(input, ok, hadBad
                ? 'Удалены русские буквы или пробелы. Пароль: от 6 символов (латиница + цифра).'
                : 'Минимум 6 символов: хотя бы 1 латинская буква и 1 цифра.');
            return ok;
        }

        if (rule === 'password-confirm') {
            const filtered = val.replace(/[^\x21-\x7E]/g, '');
            if (filtered !== val) {
                input.value = filtered;
                val = filtered;
            }
            if (!val && !showEmptyError) {
                clearFieldState(input);
                return false;
            }
            const form = input.closest('form');
            const pwdInput = form ? form.querySelector('[data-rule="password-register"]') : null;
            const ok = val.length >= 6 && pwdInput && val === pwdInput.value;
            setFieldState(input, ok, 'Пароли не совпадают.');
            return ok;
        }

        if (rule === 'password-login') {
            const filtered = val.replace(/\s+/g, '');
            if (filtered !== val) {
                input.value = filtered;
                val = filtered;
            }
            if (!val && !showEmptyError) {
                clearFieldState(input);
                return false;
            }
            const ok = val.length >= 1;
            setFieldState(input, ok, 'Введите пароль.');
            return ok;
        }

        return true;
    }

    function updatePasswordStrength(input, val, badCharRemoved) {
        const form = input.closest('form');
        if (!form) return;
        const bar = form.querySelector('.password-strength-fill');
        const label = form.querySelector('.password-strength-label');
        if (!bar || !label) return;

        let score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[A-Za-z]/.test(val) && /\d/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val) && /[A-Z]/.test(val)) score++;

        const widths = ['0%', '25%', '50%', '75%', '100%'];
        const colors = ['#e2e8f0', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
        const texts = ['Введите пароль (латиница и цифры)', 'Слабый пароль', 'Средний пароль', 'Хороший пароль', 'Надежный пароль'];

        bar.style.width = widths[score];
        bar.style.backgroundColor = colors[score];
        label.textContent = badCharRemoved ? 'Кириллица и пробелы запрещены — используйте латиницу!' : texts[score];
    }

    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[data-rule]');

        inputs.forEach(input => {
            if (input.dataset.rule === 'phone' && typeof IMask !== 'undefined') {
                IMask(input, { mask: '+{7} (000) 000-00-00' });
                input.dataset.imaskBound = '1';
            }

            input.addEventListener('input', () => sanitizeAndValidate(input, false));
            input.addEventListener('blur', () => {
                if (input.value.trim() !== '') {
                    sanitizeAndValidate(input, true);
                }
            });

            if (input.dataset.rule === 'password-login' || input.dataset.rule === 'password-register') {
                const capsEl = form.querySelector('.caps-warning');
                const checkKeys = (e) => {
                    if (!capsEl) return;
                    const isCaps = e.getModifierState && e.getModifierState('CapsLock');
                    const isCyrillicKey = e.key && /^[А-Яа-яЁё]$/.test(e.key);
                    if (isCaps || isCyrillicKey) {
                        capsEl.style.display = 'block';
                        capsEl.textContent = isCyrillicKey
                            ? 'Включена русская раскладка! Переключитесь на английскую (EN).'
                            : 'Включен CapsLock!';
                    } else if (e.type === 'keyup') {
                        capsEl.style.display = 'none';
                    }
                };
                input.addEventListener('keydown', checkKeys);
                input.addEventListener('keyup', checkKeys);
            }
        });

        form.addEventListener('submit', function(e) {
            let allValid = true;
            let firstInvalid = null;

            inputs.forEach(input => {
                const ok = sanitizeAndValidate(input, true);
                if (!ok) {
                    allValid = false;
                    if (!firstInvalid) firstInvalid = input;
                }
            });

            const agreeBox = form.querySelector('input[type="checkbox"][required]');
            if (agreeBox && !agreeBox.checked) {
                allValid = false;
                agreeBox.classList.add('is-invalid');
                if (!firstInvalid) firstInvalid = agreeBox;
            } else if (agreeBox) {
                agreeBox.classList.remove('is-invalid');
            }

            if (!allValid) {
                e.preventDefault();
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
}

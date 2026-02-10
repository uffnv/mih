document.addEventListener('DOMContentLoaded', function() {
    // Инициализация компонентов
    initSliders();
    initAddToCart();
    initCartActions();
    initMobileMenu();
    initProductGallery();
});

// --- КОРЗИНА (ДОБАВЛЕНИЕ) ---
function initAddToCart() {
    document.body.addEventListener('click', function(e) {
        // Делегирование события для кнопок .add-to-cart
        const btn = e.target.closest('.add-to-cart');
        if (btn) {
            e.preventDefault();
            const productId = btn.getAttribute('data-product-id');
            
            // Ищем поле количества (если есть ID или просто input рядом)
            let quantity = 1;
            const qtyId = btn.getAttribute('data-qty-id');
            if (qtyId) {
                const qtyInput = document.getElementById(qtyId);
                if (qtyInput) quantity = qtyInput.value;
            }

            addToCart(productId, quantity, btn);
        }
    });
}

function addToCart(productId, quantity, btn) {
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Товар добавлен!', 'success');
            updateCartCounters(data.cart_count);
        } else {
            showNotification(data.message || 'Ошибка', 'error');
        }
    })
    .catch(err => showNotification('Ошибка сети', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// --- КОРЗИНА (ДЕЙСТВИЯ: +, -, Удалить) ---
function initCartActions() {
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.cart-action-btn');
        if (!btn) return;

        e.preventDefault();
        const productId = btn.getAttribute('data-product-id');
        const action = btn.getAttribute('data-action');
        const row = btn.closest('tr');

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('action', action);

        fetch('ajax/update_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.item_quantity === 0) {
                    row.remove();
                    if (data.total_quantity === 0) location.reload(); // Перезагрузка если пусто
                } else {
                    // Обновляем кол-во в строке
                    const qtyInput = row.querySelector('.qty-input');
                    if (qtyInput) qtyInput.value = data.item_quantity;
                    
                    // Обновляем сумму строки
                    const subtotalEl = row.querySelector('.item-subtotal');
                    if (subtotalEl) subtotalEl.textContent = formatPrice(data.item_subtotal);
                }

                // Обновляем общие итоги
                document.querySelectorAll('.total-price-display').forEach(el => el.textContent = formatPrice(data.total_price));
                document.querySelectorAll('.total-qty-display').forEach(el => el.textContent = data.total_quantity + ' шт.');
                
                updateCartCounters(data.cart_count);
            }
        });
    });
}

function updateCartCounters(count) {
    // Обновляем все счетчики на странице (в шапке и т.д.)
    document.querySelectorAll('.cart-counter, .cart-btn .badge').forEach(el => {
        el.textContent = count;
        el.classList.toggle('d-none', count === 0);
    });
}

function formatPrice(price) {
    return new Intl.NumberFormat('ru-RU').format(price) + ' ₽';
}

// --- УВЕДОМЛЕНИЯ ---
function showNotification(msg, type) {
    const div = document.createElement('div');
    div.className = `notification-toast ${type}`;
    div.style.cssText = `position:fixed;top:20px;right:20px;padding:15px;background:${type==='success'?'#198754':'#dc3545'};color:#fff;border-radius:5px;z-index:9999;box-shadow:0 2px 10px rgba(0,0,0,0.2);`;
    div.textContent = msg;
    document.body.appendChild(div);
    setTimeout(() => { div.style.opacity='0'; setTimeout(()=>div.remove(), 300); }, 3000);
}

// --- СЛАЙДЕРЫ (сохраняем старую логику) ---
function initSliders() {
    const sliders = document.querySelectorAll('.slider');
    sliders.forEach(slider => {
        // Ваша логика слайдера...
    });
}

// --- МОБИЛЬНОЕ МЕНЮ ---
function initMobileMenu() {
    const btn = document.getElementById('burgerBtn');
    const menu = document.getElementById('mobileMenu');
    if(btn && menu) {
        btn.addEventListener('click', () => {
            btn.classList.toggle('active');
            menu.classList.toggle('active');
        });
    }
}

// --- ГАЛЕРЕЯ ТОВАРА ---
function initProductGallery() {
    const main = document.getElementById('main-product-image');
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.addEventListener('click', () => {
            if(main) main.src = thumb.getAttribute('data-image') || thumb.src;
        });
    });
}

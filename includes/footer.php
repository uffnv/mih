<?php
// includes/footer.php
?>
<footer class="bg-dark text-white pt-5 pb-4 mt-auto">
    <div class="container">
        <div class="row g-4">
            <!-- О компании -->
            <div class="col-md-4">
                <h5 class="text-warning fw-bold mb-3">МИХАИЛ ПОТАПЫЧ</h5>
                <p class="text-white-50 small">
                    Мы предлагаем лучшие решения для отопления вашего дома. 
                    Качественное оборудование, профессиональный монтаж и гарантия на все виды работ.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-white fs-5 hover-warning"><i class="bi bi-telegram"></i></a>
                    <a href="#" class="text-white fs-5 hover-warning"><i class="bi bi-whatsapp"></i></a>
                    <a href="#" class="text-white fs-5 hover-warning"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <!-- Навигация -->
            <div class="col-md-4">
                <h5 class="text-warning fw-bold mb-3">НАВИГАЦИЯ</h5>
                <ul class="list-unstyled text-white-50 small">
                    <li class="mb-2"><a href="catalog.php" class="text-decoration-none text-white-50 hover-white">Каталог товаров</a></li>
                    <li class="mb-2"><a href="installers.php" class="text-decoration-none text-white-50 hover-white">Наши мастера</a></li>
                    <li class="mb-2"><a href="promotions.php" class="text-decoration-none text-white-50 hover-white">Акции и скидки</a></li>
                    <li class="mb-2"><a href="contacts.php" class="text-decoration-none text-white-50 hover-white">Контакты</a></li>
                </ul>
            </div>

            <!-- Контакты -->
            <div class="col-md-4">
                <h5 class="text-warning fw-bold mb-3">КОНТАКТЫ</h5>
                <ul class="list-unstyled text-white-50 small">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-geo-alt text-warning me-2 fs-5"></i>
                        <span>г. Горно-Алтайск, ул. Ленина, 123</span>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="bi bi-telephone text-warning me-2 fs-5"></i>
                        <a href="tel:+79991234567" class="text-decoration-none text-white-50 hover-white">+7 (999) 123-45-67</a>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="bi bi-envelope text-warning me-2 fs-5"></i>
                        <a href="mailto:info@potapych.ru" class="text-decoration-none text-white-50 hover-white">info@potapych.ru</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-top border-secondary mt-4 pt-4 text-center small text-white-50">
            &copy; <?= date('Y'); ?> Михаил Потапыч. Все права защищены.
        </div>
    </div>
</footer>

<!-- Скрипты -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<!-- СКРИПТ ДОБАВЛЕНИЯ В КОРЗИНУ (catalog.php / product.php) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof GLightbox !== 'undefined') GLightbox({ selector: '.glightbox' });

    // Проверяем, чтобы не дублировать слушатели
    if (window.footerCartScriptAttached) return;
    window.footerCartScriptAttached = true;

    document.body.addEventListener('click', function(e) {
        // Ищем кнопку добавления
        const btn = e.target.closest('.btn-add-cart');
        if (!btn) return;

        e.preventDefault();
        e.stopImmediatePropagation();

        // Если уже нажата
        if (btn.disabled) return;

        // Данные
        const pid = btn.dataset.productId;
        let qty = 1;
        
        // Ищем инпут (в product.php или catalog.php)
        const wrapper = btn.closest('div') || btn.parentElement;
        const input = wrapper.querySelector('input[name="quantity"]');
        if (input) qty = input.value;

        // Визуал загрузки
        const originalHtml = btn.innerHTML;
        const w = btn.offsetWidth;
        btn.style.width = w + 'px';
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        const fd = new FormData();
        fd.append('action', 'add');
        fd.append('product_id', pid);
        fd.append('quantity', qty);

        // Используем ваш файл cart_action.php
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
                // Обновляем счетчик
                const badge = document.getElementById('cart-count-badge');
                if (badge) {
                    // Если вернул total_qty - ставим его
                    if (res.total_qty !== undefined) {
                        badge.innerText = res.total_qty;
                        badge.style.display = 'block';
                    }
                }
                
                // Зеленая галочка
                btn.classList.add('btn-success');
                const tempHtml = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check2"></i>';
                
                setTimeout(() => {
                    btn.classList.remove('btn-success');
                    btn.innerHTML = tempHtml;
                }, 1500);
            } else {
                alert(res.message || 'Ошибка');
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            btn.style.width = '';
        });
    });
});
</script>
</body>
</html>

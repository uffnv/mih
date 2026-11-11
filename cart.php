<?php
// cart.php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$user_id = $_SESSION['user_id'] ?? 0;
$cart_items = getCart($user_id);
$total_sum = 0;
foreach ($cart_items as $item) {
    $total_sum += $item['price'] * $item['quantity'];
}

$page_title = 'Корзина товаров';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-4 py-lg-5" style="background-color: var(--bg-page, #f8fafc);">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item"><a href="catalog.php" class="text-decoration-none text-muted">Каталог</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Корзина</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-cart3 text-warning"></i> Корзина покупок
            </h1>
            <?php if (!empty($cart_items)): ?>
                <span class="badge bg-dark rounded-pill px-3 py-2 fw-semibold" id="cart-badge-count">
                    <?= count($cart_items); ?> поз.
                </span>
            <?php endif; ?>
        </div>
        
        <?php if (empty($cart_items)): ?>
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-white">
                <div class="mb-3">
                    <i class="bi bi-cart-x display-1 text-muted opacity-50"></i>
                </div>
                <h3 class="fw-bold text-dark">Ваша корзина пуста</h3>
                <p class="text-muted mb-4">Выберите качественное отопительное оборудование в каталоге</p>
                <div>
                    <a href="catalog.php" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Перейти в каталог
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <!-- Список товаров -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-3">
                        <div class="table-responsive-md">
                            <table class="table table-hover align-middle mb-0 cart-table">
                                <thead style="background: #111417; color: #fff;">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">Товар</th>
                                        <th class="text-center py-3 border-0">Цена</th>
                                        <th class="text-center py-3 border-0">Кол-во</th>
                                        <th class="text-end py-3 pe-3 border-0">Сумма</th>
                                        <th class="text-center py-3 pe-4 border-0" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): 
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $itemImg = !empty($item['image']) ? $item['image'] : 'images/products/no-image.png';
                                    ?>
                                    <tr id="row-<?= $item['id'] ?>" class="border-bottom">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <a href="product.php?id=<?= $item['id'] ?>" class="me-3 flex-shrink-0">
                                                    <img src="<?= htmlspecialchars($itemImg) ?>" 
                                                         style="width: 68px; height: 68px; object-fit: contain;" 
                                                         class="rounded-3 border p-1 bg-white" 
                                                         alt="<?= htmlspecialchars($item['name']) ?>">
                                                </a>
                                                <div class="min-w-0">
                                                    <a href="product.php?id=<?= $item['id'] ?>" class="fw-bold text-dark text-decoration-none d-block mb-1">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </a>
                                                    <span class="d-md-none text-muted small d-block mb-1">
                                                        Цена: <?= number_format($item['price'], 0, '.', ' ') ?> ₽/шт.
                                                    </span>
                                                    <?php if (!empty($item['sku'])): ?>
                                                        <span class="badge bg-light text-muted border small">
                                                            Арт: <?= htmlspecialchars($item['sku']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-semibold text-nowrap">
                                            <?= number_format($item['price'], 0, '.', ' ') ?> ₽
                                        </td>
                                        <td class="text-center">
                                            <div class="input-group input-group-sm justify-content-center" style="width: 110px; margin: 0 auto;">
                                                <button class="btn btn-outline-secondary btn-update px-2" data-action="decrease" data-id="<?= $item['id'] ?>" type="button">-</button>
                                                <input type="text" class="form-control text-center px-0 bg-white fw-bold" value="<?= $item['quantity'] ?>" readonly id="qty-<?= $item['id'] ?>">
                                                <button class="btn btn-outline-secondary btn-update px-2" data-action="increase" data-id="<?= $item['id'] ?>" type="button">+</button>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-nowrap pe-3 text-dark fs-6" id="sub-<?= $item['id'] ?>">
                                            <?= number_format($subtotal, 0, '.', ' ') ?> ₽
                                        </td>
                                        <td class="text-center pe-4">
                                            <button class="btn btn-sm btn-outline-danger border-0 btn-update p-2" data-action="remove" data-id="<?= $item['id'] ?>" title="Удалить из корзины" type="button">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3 bg-light d-flex justify-content-between align-items-center">
                            <a href="catalog.php" class="btn btn-outline-dark btn-sm rounded-pill fw-semibold">
                                <i class="bi bi-arrow-left me-1"></i> Продолжить покупки
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Итоговый блок -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 85px; z-index: 10;">
                        <h5 class="fw-bold mb-3 text-dark pb-2 border-bottom">Сводка заказа</h5>
                        
                        <div class="d-flex justify-content-between text-muted mb-2 small">
                            <span>Выбрано позиций:</span>
                            <span id="items-count-display" class="fw-semibold text-dark"><?= count($cart_items) ?> поз.</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mb-3 small">
                            <span>Самовывоз:</span>
                            <span class="text-success fw-medium">Бесплатно (с. Майма)</span>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <span class="fs-5 fw-bold text-dark">Итого к оплате:</span>
                            <span id="total-sum" class="fs-3 fw-bold text-dark" style="color: #181c20 !important;">
                                <?= number_format($total_sum, 0, '.', ' ') ?> ₽
                            </span>
                        </div>

                        <a href="checkout.php" class="btn btn-warning w-100 py-3 fw-bold rounded-pill shadow-sm fs-5">
                            Оформить заказ <i class="bi bi-arrow-right ms-1"></i>
                        </a>

                        <div class="mt-3 text-center">
                            <small class="text-muted d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-shield-check text-success fs-6"></i> Гарантия производителя и официальный чек
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('.table');
    if (!table) return;

    table.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-update');
        if (!btn) return;

        e.preventDefault();
        
        if (btn.disabled) return;
        btn.disabled = true;

        const pid = btn.dataset.id;
        const action = btn.dataset.action;

        if (action === 'remove' && !confirm('Удалить товар из корзины?')) {
            btn.disabled = false;
            return;
        }

        const fd = new FormData();
        fd.append('product_id', pid);
        fd.append('action', action);

        fetch('ajax/update_cart.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            if (res.success) {
                if (action === 'remove' || res.item_quantity === 0) {
                    const row = document.getElementById('row-' + pid);
                    if (row) row.remove();
                    if (res.cart_count === 0 || document.querySelectorAll('tbody tr').length === 0) {
                        location.reload();
                    }
                } else {
                    const qtyEl = document.getElementById('qty-' + pid);
                    if (qtyEl) qtyEl.value = res.item_quantity;
                    const subEl = document.getElementById('sub-' + pid);
                    if (subEl) subEl.innerText = new Intl.NumberFormat('ru-RU').format(res.item_subtotal) + ' ₽';
                }
                const totalEl = document.getElementById('total-sum');
                if (totalEl) totalEl.innerText = new Intl.NumberFormat('ru-RU').format(res.total_price) + ' ₽';
                
                // Обновляем счетчики в шапке (десктоп + мобильная кнопка)
                document.querySelectorAll('#cart-count-badge, .cart-count-sync').forEach(badge => {
                    badge.innerText = res.cart_count;
                    badge.style.display = res.cart_count > 0 ? 'inline-block' : 'none';
                });
            } else {
                alert(res.message || 'Ошибка обновления корзины');
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

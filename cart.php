<?php
// cart.php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'Корзина';
require_once __DIR__ . '/includes/header.php';

// =========================================================================
// БЛОК 1: ПОЛУЧЕНИЕ ДАННЫХ И АВТО-ИСПРАВЛЕНИЕ ОШИБОК БАЗЫ
// =========================================================================
$final_cart_data = []; // формат: [product_id => quantity]

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // 1. Получаем содержимое корзины
    // Группируем на уровне SQL, чтобы избежать дублей записей корзины
    $stmt = $pdo->prepare("SELECT product_id, SUM(quantity) as quantity FROM cart WHERE user_id = ? GROUP BY product_id");
    $stmt->execute([$userId]);
    $raw_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($raw_rows as $row) {
        $final_cart_data[$row['product_id']] = (int)$row['quantity'];
    }

} else {
    // Для гостей берем из сессии
    $final_cart_data = $_SESSION['guest_cart'] ?? [];
}

// =========================================================================
// БЛОК 2: ПОЛУЧЕНИЕ ИНФОРМАЦИИ О ТОВАРАХ (С ЗАЩИТОЙ ОТ ДУБЛЕЙ В PRODUCTS)
// =========================================================================
$cart_items = [];
$total_sum = 0;

if (!empty($final_cart_data)) {
    $ids = array_keys($final_cart_data);
    
    // Создаем плейсхолдеры (?,?,?)
    $in = str_repeat('?,', count($ids) - 1) . '?';
    
    // ВАЖНО: Используем DISTINCT или GROUP BY, чтобы если в таблице products
    // есть дубликаты (как в вашем SQL дампе), мы взяли товар только один раз.
    $sql = "SELECT DISTINCT id, name, price, sku, old_price 
            FROM products 
            WHERE id IN ($in)";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Собираем итоговый массив
    foreach ($products as $p) {
        // Если вдруг DISTINCT не сработал (разные цены у дублей), проверяем вручную
        $isInList = false;
        foreach ($cart_items as $existing) {
            if ($existing['id'] == $p['id']) {
                $isInList = true; 
                break;
            }
        }
        if ($isInList) continue;

        // Добавляем количество из корзины
        $qty = $final_cart_data[$p['id']];
        $p['quantity'] = $qty;
        
        // Получаем изображение (берем только одно)
        $stmtImg = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ? ORDER BY is_main DESC LIMIT 1");
        $stmtImg->execute([$p['id']]);
        $img = $stmtImg->fetchColumn();
        $p['image'] = $img ? str_replace('../', '', $img) : 'images/no-photo.jpg';
        
        $cart_items[] = $p;
        $total_sum += $p['price'] * $qty;
    }
}
?>

<main class="py-5 bg-light">
    <div class="container">
        <h1 class="mb-4">Корзина</h1>
        
        <?php if (empty($cart_items)): ?>
            <div class="text-center py-5 bg-white rounded shadow-sm">
                <i class="bi bi-cart-x display-1 text-muted mb-3 d-block"></i>
                <h3 class="text-muted">Корзина пуста</h3>
                <a href="catalog.php" class="btn btn-warning mt-3">Перейти в каталог</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <!-- Список товаров -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Товар</th>
                                        <th class="text-center">Цена</th>
                                        <th class="text-center">Кол-во</th>
                                        <th class="text-end pe-4">Сумма</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): 
                                        $subtotal = $item['price'] * $item['quantity'];
                                    ?>
                                    <tr class="border-bottom" id="row-<?= $item['id'] ?>">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <a href="product.php?id=<?= $item['id'] ?>">
                                                    <img src="<?= htmlspecialchars($item['image']) ?>" style="width: 60px; height: 60px; object-fit: cover;" class="rounded border me-3">
                                                </a>
                                                <div>
                                                    <a href="product.php?id=<?= $item['id'] ?>" class="fw-bold text-dark text-decoration-none"><?= htmlspecialchars($item['name']) ?></a>
                                                    <div class="small text-muted"><?= htmlspecialchars($item['sku']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><?= number_format($item['price'], 0, '.', ' ') ?> ₽</td>
                                        <td class="text-center">
                                            <div class="input-group input-group-sm justify-content-center" style="width: 110px; margin: 0 auto;">
                                                <button class="btn btn-outline-secondary btn-update" data-action="decrease" data-id="<?= $item['id'] ?>">-</button>
                                                <input type="text" class="form-control text-center px-0 bg-white" value="<?= $item['quantity'] ?>" readonly id="qty-<?= $item['id'] ?>">
                                                <button class="btn btn-outline-secondary btn-update" data-action="increase" data-id="<?= $item['id'] ?>">+</button>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold pe-4" id="sub-<?= $item['id'] ?>">
                                            <?= number_format($subtotal, 0, '.', ' ') ?> ₽
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-link text-danger p-0 border-0 btn-update" data-action="remove" data-id="<?= $item['id'] ?>">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Итоговый блок -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
                        <h5 class="fw-bold">Итого</h5>
                        <div class="d-flex justify-content-between fs-4 fw-bold mb-4">
                            <span>К оплате:</span>
                            <span id="total-sum"><?= number_format($total_sum, 0, '.', ' ') ?> ₽</span>
                        </div>
                        <a href="checkout.php" class="btn btn-warning w-100 py-3 fw-bold">Оформить заказ</a>
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

        if (action === 'remove' && !confirm('Удалить товар?')) {
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
                    // Если удалили последний товар, перезагружаем страницу чтобы показать "Корзина пуста"
                    if (res.cart_count === 0 || document.querySelectorAll('tbody tr').length === 0) {
                        location.reload();
                    }
                } else {
                    document.getElementById('qty-' + pid).value = res.item_quantity;
                    document.getElementById('sub-' + pid).innerText = new Intl.NumberFormat('ru-RU').format(res.item_subtotal) + ' ₽';
                }
                document.getElementById('total-sum').innerText = new Intl.NumberFormat('ru-RU').format(res.total_price) + ' ₽';
                
                // Обновляем счетчик в шапке если есть
                const badge = document.getElementById('cart-count-badge');
                if (badge) {
                    badge.innerText = res.cart_count;
                    badge.style.display = res.cart_count > 0 ? 'inline-block' : 'none'; // или block/flex в зависимости от верстки
                }
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

<?php
require_once '../includes/config.php';
require_once 'admin_header.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = null;

if ($order_id) {
    // Получаем данные заказа
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // Получаем товары заказа
    // ИСПРАВЛЕННЫЙ ЗАПРОС: Берем первое попавшееся изображение из product_images
    $stmt = $pdo->prepare("
        SELECT 
            oi.*, 
            p.name, 
            (SELECT image_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_main DESC LIMIT 1) as image_path
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (!$order) {
    echo '<div class="alert alert-danger">Заказ не найден.</div>';
    require_once 'admin_footer.php';
    exit;
}

// Обработка смены статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new_status, $order_id]);
    // Обновляем страницу
    echo "<script>window.location.href='order_details.php?id=$order_id';</script>";
    exit;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Заказ #<?= $order['id']; ?></h1>
    <a href="orders.php" class="btn btn-secondary">К списку заказов</a>
</div>

<div class="row g-4">
    <!-- Информация о заказе -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Состав заказа</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Товар</th>
                                <th class="text-center">Цена</th>
                                <th class="text-center">Кол-во</th>
                                <th class="text-end">Итого</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="../<?= htmlspecialchars($item['image_path']); ?>" width="40" height="40" class="rounded me-2 object-fit-cover">
                                        <?php else: ?>
                                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center text-muted" style="width: 40px; height: 40px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($item['name'] ?? 'Товар удален'); ?></div>
                                            <small class="text-muted">ID: <?= $item['product_id']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-nowrap"><?= number_format($item['price'], 0, ',', ' '); ?> ₽</td>
                                <td class="text-center"><?= $item['quantity']; ?></td>
                                <td class="text-end text-nowrap fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', ' '); ?> ₽</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Общая сумма:</td>
                                <td class="text-end fw-bold fs-5"><?= number_format($order['total_amount'], 0, ',', ' '); ?> ₽</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Информация о клиенте и статус -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Статус заказа</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Текущий статус</label>
                        <select name="status" class="form-select">
                            <option value="new" <?= $order['status'] == 'new' ? 'selected' : ''; ?>>Новый</option>
                            <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : ''; ?>>В обработке</option>
                            <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : ''; ?>>Отправлен</option>
                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : ''; ?>>Выполнен</option>
                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Отменен</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn btn-primary w-100">Обновить статус</button>
                </form>
                <div class="mt-3 text-muted small">
                    Дата создания: <?= date('d.m.Y H:i', strtotime($order['created_at'])); ?>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Клиент</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Имя:</strong> <br>
                    <?= htmlspecialchars($order['full_name'] ?? $order['customer_name'] ?? 'Не указано'); ?>
                </div>
                <div class="mb-2">
                    <strong>Телефон:</strong> <br>
                    <a href="tel:<?= htmlspecialchars($order['phone'] ?? $order['customer_phone'] ?? ''); ?>"><?= htmlspecialchars($order['phone'] ?? $order['customer_phone'] ?? ''); ?></a>
                </div>
                <div class="mb-2">
                    <strong>Email:</strong> <br>
                    <?php if (!empty($order['email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($order['email']); ?>"><?= htmlspecialchars($order['email']); ?></a>
                    <?php elseif (!empty($order['customer_email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($order['customer_email']); ?>"><?= htmlspecialchars($order['customer_email']); ?></a>
                    <?php else: ?>
                        <span class="text-muted">Не указан</span>
                    <?php endif; ?>
                </div>
                <div class="mb-2">
                    <strong>Адрес:</strong> <br>
                    <?= nl2br(htmlspecialchars($order['address'] ?? $order['delivery_address'] ?? 'Не указан')); ?>
                </div>
                <div class="mb-0">
                    <strong>Комментарий:</strong> <br>
                    <span class="fst-italic text-muted"><?= nl2br(htmlspecialchars($order['comment'] ?? 'Нет комментария')); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

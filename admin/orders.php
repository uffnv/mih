<?php
require_once '../includes/config.php';
$page_title = 'Управление заказами';
require_once 'admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }
    $order_id = (int)$_POST['order_id'];
    $status = trim($_POST['status'] ?? '');
    $allowed_statuses = ['pending', 'confirmed', 'processing', 'completed', 'cancelled'];
    
    if (!in_array($status, $allowed_statuses, true)) {
        $error = "Недопустимый статус заказа.";
    } else {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        if ($stmt->execute([$status, $order_id])) {
            $success = "Статус заказа #$order_id успешно изменен на «" . htmlspecialchars($status) . "»!";
        } else {
            $error = "Ошибка при обновлении статуса.";
        }
    }
}

// Фильтр по статусу
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : 'all';
$sql = "SELECT * FROM orders";
$params = [];
if ($filter_status !== 'all' && in_array($filter_status, ['pending', 'confirmed', 'processing', 'completed', 'cancelled'])) {
    $sql .= " WHERE status = ?";
    $params[] = $filter_status;
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Заказы (<?= count($orders) ?>)</h1>
        <small class="text-muted">Управление заказами и изменением статусов</small>
    </div>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i> <?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Фильтр по статусам -->
<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="orders.php?status=all" class="btn btn-sm rounded-pill <?= $filter_status === 'all' ? 'btn-dark fw-bold' : 'btn-outline-secondary' ?>">
        Все заказы
    </a>
    <a href="orders.php?status=pending" class="btn btn-sm rounded-pill <?= $filter_status === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning text-dark' ?>">
        Новые
    </a>
    <a href="orders.php?status=confirmed" class="btn btn-sm rounded-pill <?= $filter_status === 'confirmed' ? 'btn-info text-dark fw-bold' : 'btn-outline-info text-dark' ?>">
        Подтвержденные
    </a>
    <a href="orders.php?status=processing" class="btn btn-sm rounded-pill <?= $filter_status === 'processing' ? 'btn-primary fw-bold' : 'btn-outline-primary' ?>">
        В обработке
    </a>
    <a href="orders.php?status=completed" class="btn btn-sm rounded-pill <?= $filter_status === 'completed' ? 'btn-success fw-bold' : 'btn-outline-success' ?>">
        Выполненные
    </a>
    <a href="orders.php?status=cancelled" class="btn btn-sm rounded-pill <?= $filter_status === 'cancelled' ? 'btn-danger fw-bold' : 'btn-outline-danger' ?>">
        Отмененные
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3">№</th>
                        <th class="py-3">Дата</th>
                        <th class="py-3">Покупатель</th>
                        <th class="py-3">Телефон</th>
                        <th class="py-3">Сумма</th>
                        <th class="py-3">Статус</th>
                        <th class="text-end pe-3 py-3">Управление</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): 
                            $st = $order['status'];
                            $badgeClass = 'bg-secondary';
                            $status_text = $st;
                            
                            switch($st) {
                                case 'pending': 
                                    $badgeClass = 'bg-warning text-dark'; 
                                    $status_text = 'Новый';
                                    break;
                                case 'confirmed': 
                                    $badgeClass = 'bg-info text-dark'; 
                                    $status_text = 'Подтвержден';
                                    break;
                                case 'processing': 
                                    $badgeClass = 'bg-primary'; 
                                    $status_text = 'В работе';
                                    break;
                                case 'completed': 
                                    $badgeClass = 'bg-success'; 
                                    $status_text = 'Выполнен';
                                    break;
                                case 'cancelled': 
                                    $badgeClass = 'bg-danger'; 
                                    $status_text = 'Отменен';
                                    break;
                            }
                        ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">#<?= $order['id']; ?></td>
                                <td class="text-muted small"><?= date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($order['customer_name'] ?? 'Покупатель'); ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($order['customer_email'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($order['customer_phone'])): ?>
                                        <a href="tel:<?= htmlspecialchars($order['customer_phone']); ?>" class="text-decoration-none text-dark fw-bold">
                                            <?= htmlspecialchars($order['customer_phone']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-dark"><?= number_format($order['total_price'], 0, '.', ' '); ?> ₽</td>
                                <td>
                                    <span class="badge <?= $badgeClass; ?> rounded-pill px-2 py-1"><?= htmlspecialchars($status_text); ?></span>
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#orderModal<?= $order['id']; ?>">
                                        <i class="bi bi-eye me-1"></i> Состав и статус
                                    </button>
                                    
                                    <!-- Модальное окно заказа -->
                                    <div class="modal fade" id="orderModal<?= $order['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered text-start">
                                            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title fw-bold">Заказ #<span class="text-warning"><?= $order['id']; ?></span></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 bg-white">
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3 h-100 border">
                                                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person me-1 text-warning"></i>Данные клиента</h6>
                                                                <div class="small">
                                                                    <div><strong>Имя:</strong> <?= htmlspecialchars($order['customer_name'] ?? '—'); ?></div>
                                                                    <div><strong>Телефон:</strong> <?= htmlspecialchars($order['customer_phone'] ?? '—'); ?></div>
                                                                    <div><strong>Email:</strong> <?= htmlspecialchars($order['customer_email'] ?? '—'); ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-light rounded-3 h-100 border">
                                                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-truck me-1 text-warning"></i>Доставка и оплата</h6>
                                                                <div class="small">
                                                                    <div><strong>Способ:</strong> <?= $order['delivery_method'] === 'pickup' ? 'Самовывоз' : 'Курьер'; ?></div>
                                                                    <div><strong>Адрес:</strong> <?= htmlspecialchars($order['delivery_address'] ?? 'Самовывоз со склада'); ?></div>
                                                                    <div><strong>Оплата:</strong> <?= $order['payment_method'] === 'card' ? 'Банковской картой' : 'Наличными'; ?></div>
                                                                    <?php if (!empty($order['comment'])): ?>
                                                                        <div class="mt-2 text-muted fst-italic">"<?= nl2br(htmlspecialchars($order['comment'])); ?>"</div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-seam me-1 text-warning"></i>Товары в заказе</h6>
                                                    <div class="table-responsive mb-4">
                                                        <table class="table table-bordered table-sm align-middle mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Товар</th>
                                                                    <th class="text-end">Цена</th>
                                                                    <th class="text-center">Кол-во</th>
                                                                    <th class="text-end">Итого</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                                                                $stmtItems->execute([$order['id']]);
                                                                $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                                                                foreach ($items as $item):
                                                                ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($item['product_name']); ?></td>
                                                                    <td class="text-end"><?= number_format($item['price'], 0, '.', ' '); ?> ₽</td>
                                                                    <td class="text-center"><?= $item['quantity']; ?> шт.</td>
                                                                    <td class="text-end fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, '.', ' '); ?> ₽</td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                                <tr class="table-light">
                                                                    <td colspan="3" class="text-end fw-bold">Итоговая сумма:</td>
                                                                    <td class="text-end fw-bold fs-6 text-dark"><?= number_format($order['total_price'], 0, '.', ' '); ?> ₽</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    
                                                    <hr>
                                                    <form method="POST" class="d-flex flex-wrap align-items-center gap-2">
                                                        <?= getCsrfField(); ?>
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                                        <label class="fw-bold text-dark small">Сменить статус:</label>
                                                        <select name="status" class="form-select form-select-sm w-auto rounded-pill">
                                                            <option value="pending" <?= $st == 'pending' ? 'selected' : ''; ?>>Новый</option>
                                                            <option value="confirmed" <?= $st == 'confirmed' ? 'selected' : ''; ?>>Подтвержден</option>
                                                            <option value="processing" <?= $st == 'processing' ? 'selected' : ''; ?>>В обработке</option>
                                                            <option value="completed" <?= $st == 'completed' ? 'selected' : ''; ?>>Выполнен</option>
                                                            <option value="cancelled" <?= $st == 'cancelled' ? 'selected' : ''; ?>>Отменен</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-warning btn-sm fw-bold rounded-pill px-3 shadow-sm">
                                                            Обновить статус
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 opacity-50 d-block mb-2"></i>
                                Заказы не найдены
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

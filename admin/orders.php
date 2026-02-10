<?php
require_once '../includes/config.php';
require_once 'admin_header.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die('Access denied');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $order_id])) {
        $success = "Статус заказа #$order_id успешно обновлен!";
    } else {
        $error = "Ошибка обновления статуса.";
    }
}

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Управление заказами</h2>
    </div>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Дата</th>
                            <th>Клиент</th>
                            <th>Телефон</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id']; ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <?= htmlspecialchars($order['customer_name'] ?? 'Гость'); ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($order['customer_phone'] ?? ''); ?>
                                    </td>
                                    <td class="fw-bold">
                                        <?= number_format($order['total_price'], 0, ',', ' '); ?> ₽
                                    </td>
                                    <td>
                                        <?php 
                                            $st = $order['status'];
                                            // Перевод статусов
                                            $status_text = $st;
                                            $badgeClass = 'bg-secondary';
                                            
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
                                                    $status_text = 'В обработке';
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
                                        <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($status_text); ?></span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal<?= $order['id']; ?>">
                                            <i class="bi bi-eye"></i> Детали
                                        </button>
                                        
                                        <div class="modal fade" id="orderModal<?= $order['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Заказ #<?= $order['id']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <h6>Клиент</h6>
                                                                <p>
                                                                    <strong>Имя:</strong> <?= htmlspecialchars($order['customer_name'] ?? ''); ?><br>
                                                                    <strong>Тел:</strong> <?= htmlspecialchars($order['customer_phone'] ?? ''); ?><br>
                                                                    <strong>Email:</strong> <?= htmlspecialchars($order['customer_email'] ?? ''); ?>
                                                                </p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Доставка</h6>
                                                                <p>
                                                                    <strong>Адрес:</strong> <?= htmlspecialchars($order['delivery_address'] ?? ''); ?><br>
                                                                    <strong>Метод:</strong> <?= htmlspecialchars($order['delivery_method']); ?><br>
                                                                    <strong>Оплата:</strong> <?= htmlspecialchars($order['payment_method']); ?>
                                                                </p>
                                                                <?php if($order['comment']): ?>
                                                                    <p><strong>Комментарий:</strong> <?= nl2br(htmlspecialchars($order['comment'])); ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <h6>Товары</h6>
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Название</th>
                                                                    <th>Цена</th>
                                                                    <th>Кол-во</th>
                                                                    <th>Итого</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                                                                $stmtItems->execute([$order['id']]);
                                                                $items = $stmtItems->fetchAll();
                                                                foreach ($items as $item):
                                                                ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($item['product_name']); ?></td>
                                                                    <td><?= number_format($item['price'], 0, ',', ' '); ?> ₽</td>
                                                                    <td><?= $item['quantity']; ?></td>
                                                                    <td><?= number_format($item['price'] * $item['quantity'], 0, ',', ' '); ?> ₽</td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                                <tr>
                                                                    <td colspan="3" class="text-end fw-bold">Общая сумма:</td>
                                                                    <td class="fw-bold"><?= number_format($order['total_price'], 0, ',', ' '); ?> ₽</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        
                                                        <hr>
                                                        <form method="POST" class="d-flex align-items-center">
                                                            <input type="hidden" name="action" value="update_status">
                                                            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                                            <label class="me-2 fw-bold">Статус:</label>
                                                            <select name="status" class="form-select me-2 w-auto">
                                                                <option value="pending" <?= $st == 'pending' ? 'selected' : ''; ?>>Новый</option>
                                                                <option value="confirmed" <?= $st == 'confirmed' ? 'selected' : ''; ?>>Подтвержден</option>
                                                                <option value="processing" <?= $st == 'processing' ? 'selected' : ''; ?>>В обработке</option>
                                                                <option value="completed" <?= $st == 'completed' ? 'selected' : ''; ?>>Выполнен</option>
                                                                <option value="cancelled" <?= $st == 'cancelled' ? 'selected' : ''; ?>>Отменен</option>
                                                            </select>
                                                            <button type="submit" class="btn btn-success">Сохранить</button>
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
                                <td colspan="7" class="text-center py-4">Заказов пока нет</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

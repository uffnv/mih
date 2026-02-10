<?php
require_once '../includes/config.php';
require_once 'admin_header.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    die('Access denied');
}

// Статистика
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 0");
$pending_reviews = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
$users_count = $stmt->fetchColumn();

// Последние заказы
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();
?>

<div class="container py-5">
    <h1 class="mb-4">Панель управления</h1>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Заказы</h6>
                            <h2 class="mt-2 mb-0"><?= $total_orders; ?></h2>
                        </div>
                        <i class="bi bi-cart fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Товары</h6>
                            <h2 class="mt-2 mb-0"><?= $total_products; ?></h2>
                        </div>
                        <i class="bi bi-box-seam fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Отзывы</h6>
                            <h2 class="mt-2 mb-0"><?= $pending_reviews; ?></h2>
                            <small>Ожидают</small>
                        </div>
                        <i class="bi bi-chat-text fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Пользователи</h6>
                            <h2 class="mt-2 mb-0"><?= $users_count; ?></h2>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Последние заказы</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Клиент</th>
                            <th>Телефон</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th class="text-end">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recent_orders) > 0): ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id']; ?></td>
                                    <td><?= htmlspecialchars($order['customer_name'] ?? 'Гость'); ?></td>
                                    <td><?= htmlspecialchars($order['customer_phone'] ?? ''); ?></td>
                                    <td class="fw-bold"><?= number_format($order['total_price'], 0, ',', ' '); ?> ₽</td>
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
                                    <td><?= date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td class="text-end">
                                        <a href="orders.php" class="btn btn-sm btn-outline-primary">Подробнее</a>
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

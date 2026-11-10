<?php
require_once '../includes/config.php';
$page_title = 'Панель управления — Территория Тепла';
require_once 'admin_header.php';

// Статистика
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = (int)$stmt->fetchColumn();

$stmt = $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM orders WHERE status != 'cancelled'");
$total_revenue = (float)$stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products = (int)$stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM product_reviews WHERE is_approved = 0");
$pending_reviews = (int)$stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM installer_applications WHERE status = 'new'");
$pending_installer_apps = (int)$stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer' OR role = 'user'");
$users_count = (int)$stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM contact_requests WHERE status != 'processed'");
$unread_requests = (int)$stmt->fetchColumn();

// Последние заказы
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 6");
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Панель управления</h1>
        <small class="text-muted">Общая статистика и оперативная сводка магазина «Территория Тепла»</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <a href="product_edit.php" class="btn btn-warning fw-bold shadow-sm rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> Добавить товар
        </a>
        <a href="promotion_edit.php" class="btn btn-dark fw-bold shadow-sm rounded-pill px-3">
            <i class="bi bi-megaphone me-1"></i> Создать акцию
        </a>
    </div>
</div>

<!-- Карточки статистики -->
<div class="row g-3 mb-4">
    <!-- Выручка -->
    <div class="col-sm-6 col-xl-3">
        <div class="card admin-card bg-white h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Выручка</div>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= number_format($total_revenue, 0, '.', ' ') ?> ₽</h3>
                    <small class="text-success"><i class="bi bi-graph-up-arrow"></i> Без учета отмен</small>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Заказы -->
    <div class="col-sm-6 col-xl-3">
        <div class="card admin-card bg-white h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Всего заказов</div>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $total_orders ?></h3>
                    <a href="orders.php" class="small text-decoration-none text-warning fw-bold">Все заказы &rarr;</a>
                </div>
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-cart-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Товары -->
    <div class="col-sm-6 col-xl-3">
        <div class="card admin-card bg-white h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Товаров в каталоге</div>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $total_products ?></h3>
                    <a href="products.php" class="small text-decoration-none text-primary fw-bold">Каталог товаров &rarr;</a>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Клиенты -->
    <div class="col-sm-6 col-xl-3">
        <div class="card admin-card bg-white h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Покупателей</div>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $users_count ?></h3>
                    <a href="users.php" class="small text-decoration-none text-info fw-bold">Список клиентов &rarr;</a>
                </div>
                <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Оповещения требующие внимания -->
<?php if ($pending_reviews > 0 || $pending_installer_apps > 0 || $unread_requests > 0): ?>
    <div class="row g-3 mb-4">
        <?php if ($pending_installer_apps > 0): ?>
            <div class="col-md-4">
                <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex justify-content-between align-items-center mb-0 p-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus-fill fs-3 text-warning"></i>
                        <div>
                            <div class="fw-bold text-dark">Анкеты мастеров (<?= $pending_installer_apps ?>)</div>
                            <small class="text-muted">Новые заявки на партнерство</small>
                        </div>
                    </div>
                    <a href="installer_applications.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-3">Анкеты</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($pending_reviews > 0): ?>
            <div class="col-md-4">
                <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex justify-content-between align-items-center mb-0 p-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-chat-dots-fill fs-3 text-warning"></i>
                        <div>
                            <div class="fw-bold text-dark">Отзывы о товарах (<?= $pending_reviews ?>)</div>
                            <small class="text-muted">Ожидают модерации</small>
                        </div>
                    </div>
                    <a href="reviews.php" class="btn btn-dark btn-sm fw-bold rounded-pill px-3">Проверить</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($unread_requests > 0): ?>
            <div class="col-md-4">
                <div class="alert alert-info border-0 shadow-sm rounded-4 d-flex justify-content-between align-items-center mb-0 p-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-exclamation-fill fs-3 text-info"></i>
                        <div>
                            <div class="fw-bold text-dark">Заявки с сайта (<?= $unread_requests ?>)</div>
                            <small class="text-muted">Вопросы с формы контактов</small>
                        </div>
                    </div>
                    <a href="contact_requests.php" class="btn btn-info btn-sm text-dark fw-bold rounded-pill px-3">Открыть</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Таблица последних заказов -->
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold fs-6">
            <i class="bi bi-clock-history text-warning me-2"></i> Последние поступившие заказы
        </h5>
        <a href="orders.php" class="btn btn-sm btn-outline-warning rounded-pill px-3">Все заказы</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3">№</th>
                        <th class="py-3">Клиент</th>
                        <th class="py-3">Телефон</th>
                        <th class="py-3">Сумма</th>
                        <th class="py-3">Статус</th>
                        <th class="py-3">Дата</th>
                        <th class="text-end pe-3 py-3">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_orders)): ?>
                        <?php foreach ($recent_orders as $order): 
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
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($order['customer_name'] ?: 'Покупатель'); ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($order['customer_email'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($order['customer_phone'])): ?>
                                        <a href="tel:<?= htmlspecialchars($order['customer_phone']); ?>" class="text-decoration-none text-dark fw-semibold">
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
                                <td class="text-muted small"><?= date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                <td class="text-end pe-3">
                                    <a href="order_details.php?id=<?= $order['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                        Детали
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox display-4 opacity-50 d-block mb-2"></i>
                                Заказов пока не поступало
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

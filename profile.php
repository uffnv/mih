<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=profile');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$user = getUserById($userId);
if (!$user) {
    header('Location: logout.php');
    exit;
}

$orders = getOrdersByUserId($userId);

// Загрузка отзывов пользователя
$product_reviews = [];
$installer_reviews = [];
$site_reviews = [];

// Отзывы о товарах
try {
    $stmt = $pdo->prepare("SELECT pr.*, p.name as product_name, p.id as pid FROM product_reviews pr JOIN products p ON pr.product_id = p.id WHERE pr.user_id = ? ORDER BY pr.created_at DESC");
    $stmt->execute([$userId]);
    $product_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

// Отзывы о мастерах
try {
    $stmt = $pdo->prepare("SELECT ir.*, i.first_name, i.last_name, i.id as installer_id FROM installer_reviews ir JOIN installers i ON ir.installer_id = i.id WHERE ir.user_id = ? ORDER BY ir.created_at DESC");
    $stmt->execute([$userId]);
    $installer_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

// Отзывы о магазине
try {
    $stmt = $pdo->prepare("SELECT * FROM site_reviews WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $site_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {}

$active_tab = 'orders';
$success_msg = ''; 
$error_msg = '';

// Обработка форм
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Удаление отзыва о магазине
    if (isset($_POST['delete_site_review'])) {
        $active_tab = 'reviews';
        $pdo->prepare("DELETE FROM site_reviews WHERE id = ? AND user_id = ?")->execute([(int)$_POST['review_id'], $userId]);
        $success_msg = "Отзыв о магазине удален.";
        $stmt = $pdo->prepare("SELECT * FROM site_reviews WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $site_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Удаление отзыва о товаре
    if (isset($_POST['delete_product_review'])) {
        $active_tab = 'reviews';
        $pdo->prepare("DELETE FROM product_reviews WHERE id = ? AND user_id = ?")->execute([(int)$_POST['review_id'], $userId]);
        $success_msg = "Отзыв о товаре удален.";
        $stmt = $pdo->prepare("SELECT pr.*, p.name as product_name, p.id as pid FROM product_reviews pr JOIN products p ON pr.product_id = p.id WHERE pr.user_id = ? ORDER BY pr.created_at DESC");
        $stmt->execute([$userId]);
        $product_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Удаление отзыва о мастере
    if (isset($_POST['delete_installer_review'])) {
        $active_tab = 'reviews';
        $pdo->prepare("DELETE FROM installer_reviews WHERE id = ? AND user_id = ?")->execute([(int)$_POST['review_id'], $userId]);
        $success_msg = "Отзыв о мастере удален.";
        try {
            $stmt = $pdo->prepare("SELECT ir.*, i.first_name, i.last_name, i.id as installer_id FROM installer_reviews ir JOIN installers i ON ir.installer_id = i.id WHERE ir.user_id = ? ORDER BY ir.created_at DESC");
            $stmt->execute([$userId]);
            $installer_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {}
    }

    // Обновление личных данных
    if (isset($_POST['update_personal'])) {
        $active_tab = 'settings';
        $current_password = $_POST['current_password'] ?? '';
        
        if (empty($current_password) || !password_verify($current_password, $user['password'])) {
            $error_msg = 'Ошибка: неверно введен текущий пароль для подтверждения изменений.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, address_line1 = ? WHERE id = ?");
            if ($stmt->execute([
                trim($_POST['first_name'] ?? ''), 
                trim($_POST['last_name'] ?? ''), 
                trim($_POST['phone'] ?? ''),
                trim($_POST['address_line1'] ?? ''), 
                $userId
            ])) {
                $success_msg = 'Личные данные успешно обновлены.';
                $user = getUserById($userId);
            } else {
                $error_msg = 'Ошибка при обновлении профиля.';
            }
        }
    }

    // Смена пароля
    if (isset($_POST['update_password'])) {
        $active_tab = 'settings';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';
        $curr_pass = $_POST['current_password_for_pass'] ?? '';

        if (!password_verify($curr_pass, $user['password'])) {
            $error_msg = 'Неверный текущий пароль.';
        } elseif (strlen($new_pass) < 6) {
            $error_msg = 'Новый пароль слишком короткий (минимум 6 символов).';
        } elseif ($new_pass !== $confirm_pass) {
            $error_msg = 'Новые пароли не совпадают.';
        } else {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$new_hash, $userId])) {
                $success_msg = 'Пароль успешно изменен!';
                $user = getUserById($userId);
            } else {
                $error_msg = 'Ошибка при сохранении пароля.';
            }
        }
    }
}

function getStatusBadge($status) {
    $map = [
        'pending'    => ['bg' => 'bg-warning text-dark', 'label' => 'В обработке'], 
        'processing' => ['bg' => 'bg-primary text-white', 'label' => 'Комплектуется'], 
        'confirmed'  => ['bg' => 'bg-info text-dark', 'label' => 'Подтвержден'],
        'completed'  => ['bg' => 'bg-success text-white', 'label' => 'Выполнен'], 
        'cancelled'  => ['bg' => 'bg-danger text-white', 'label' => 'Отменен']
    ];
    $s = $map[$status] ?? ['bg' => 'bg-secondary text-white', 'label' => $status];
    return "<span class='badge {$s['bg']} rounded-pill px-3 py-1'>{$s['label']}</span>";
}

$page_title = 'Личный кабинет — ' . htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) . ' | Территория Тепла';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-4 py-lg-5" style="background-color: var(--bg-page, #f8fafc);">
    <div class="container">
        
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Личный кабинет</li>
            </ol>
        </nav>

        <!-- Карточка пользователя -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3 text-center text-md-start">
                    <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; font-size: 1.5rem;">
                        <?= mb_substr($user['first_name'] ?: ($user['username'] ?: 'У'), 0, 1) ?>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">
                            <?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: htmlspecialchars($user['username']) ?>
                        </h4>
                        <div class="text-muted small">
                            <span class="me-3"><i class="bi bi-telephone text-warning me-1"></i><?= htmlspecialchars($user['phone'] ?? 'Телефон не указан') ?></span>
                            <?php if (!empty($user['email'])): ?>
                                <span><i class="bi bi-envelope text-warning me-1"></i><?= htmlspecialchars($user['email']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <?php if ($user['role'] === 'admin'): ?>
                        <a href="admin/index.php" class="btn btn-dark btn-sm rounded-pill fw-semibold px-3">
                            <i class="bi bi-gear-fill me-1"></i> Панель админа
                        </a>
                    <?php elseif ($user['role'] === 'installer'): ?>
                        <a href="installers/index.php" class="btn btn-warning btn-sm rounded-pill fw-bold px-3">
                            <i class="bi bi-tools me-1"></i> Кабинет мастера
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill fw-semibold px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Выйти
                    </a>
                </div>
            </div>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($success_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Навигационные вкладки -->
        <ul class="nav nav-pills mb-4 gap-2">
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 <?= $active_tab === 'orders' ? 'active bg-dark' : 'bg-white text-dark border' ?>" data-bs-toggle="pill" data-bs-target="#tab-orders">
                    <i class="bi bi-bag-check me-1"></i> Мои заказы (<?= count($orders) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 <?= $active_tab === 'reviews' ? 'active bg-dark' : 'bg-white text-dark border' ?>" data-bs-toggle="pill" data-bs-target="#tab-reviews">
                    <i class="bi bi-star me-1"></i> Мои отзывы (<?= count($product_reviews) + count($installer_reviews) + count($site_reviews) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 <?= $active_tab === 'settings' ? 'active bg-dark' : 'bg-white text-dark border' ?>" data-bs-toggle="pill" data-bs-target="#tab-settings">
                    <i class="bi bi-sliders me-1"></i> Настройки профиля
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- 1. Мои заказы -->
            <div class="tab-pane fade <?= $active_tab === 'orders' ? 'show active' : '' ?>" id="tab-orders">
                <?php if (!empty($orders)): ?>
                    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 py-3">№ Заказа</th>
                                        <th class="py-3">Дата</th>
                                        <th class="py-3">Способ доставки</th>
                                        <th class="py-3">Сумма</th>
                                        <th class="py-3">Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $ord): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">#<?= $ord['id'] ?></td>
                                        <td class="small text-muted"><?= date('d.m.Y H:i', strtotime($ord['created_at'])) ?></td>
                                        <td class="small">
                                            <?= $ord['delivery_method'] === 'courier' ? '<i class="bi bi-truck me-1"></i> Доставка' : '<i class="bi bi-geo-alt me-1"></i> Самовывоз' ?>
                                        </td>
                                        <td class="fw-bold text-dark text-nowrap"><?= number_format($ord['total_price'], 0, '.', ' ') ?> ₽</td>
                                        <td><?= getStatusBadge($ord['status']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-white">
                        <i class="bi bi-bag-x display-3 text-muted opacity-50 mb-3"></i>
                        <h4 class="fw-bold">У вас пока нет оформленных заказов</h4>
                        <p class="text-muted mb-4">Выберите печи, котлы или комплектующие в каталоге</p>
                        <div>
                            <a href="catalog.php" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">
                                Перейти в каталог
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. Мои отзывы -->
            <div class="tab-pane fade <?= $active_tab === 'reviews' ? 'show active' : '' ?>" id="tab-reviews">
                <div class="row g-4">
                    <!-- Отзывы о товарах -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                <i class="bi bi-box-seam text-warning me-2"></i> Отзывы о товарах
                            </h5>
                            <?php if (!empty($product_reviews)): ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($product_reviews as $pr): ?>
                                        <div class="p-3 bg-light rounded-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <a href="product.php?id=<?= $pr['pid'] ?>" class="fw-bold text-dark text-decoration-none">
                                                    <?= htmlspecialchars($pr['product_name']) ?>
                                                </a>
                                                <span class="badge bg-warning text-dark"><?= $pr['rating'] ?> ★</span>
                                            </div>
                                            <p class="small text-muted mb-2"><?= nl2br(htmlspecialchars($pr['comment'] ?? '')) ?></p>
                                            <form method="POST" onsubmit="return confirm('Удалить отзыв?');">
                                                <input type="hidden" name="review_id" value="<?= $pr['id'] ?>">
                                                <button type="submit" name="delete_product_review" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill small">Удалить</button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-0">Вы пока не оставляли отзывов о товарах.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Отзывы о магазине -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                <i class="bi bi-shop text-warning me-2"></i> Отзывы о магазине
                            </h5>
                            <?php if (!empty($site_reviews)): ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($site_reviews as $sr): ?>
                                        <div class="p-3 bg-light rounded-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="small text-muted"><?= date('d.m.Y', strtotime($sr['created_at'])) ?></span>
                                                <span class="badge bg-warning text-dark"><?= $sr['rating'] ?> ★</span>
                                            </div>
                                            <p class="small text-muted mb-2"><?= nl2br(htmlspecialchars($sr['comment'] ?? '')) ?></p>
                                            <form method="POST" onsubmit="return confirm('Удалить отзыв?');">
                                                <input type="hidden" name="review_id" value="<?= $sr['id'] ?>">
                                                <button type="submit" name="delete_site_review" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill small">Удалить</button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mb-0">Вы пока не оставляли отзывов о работе магазина.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Настройки профиля -->
            <div class="tab-pane fade <?= $active_tab === 'settings' ? 'show active' : '' ?>" id="tab-settings">
                <div class="row g-4">
                    <!-- Редактирование личных данных -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Личные данные</h5>
                            <form method="POST">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Имя</label>
                                        <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">Фамилия</label>
                                        <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Телефон</label>
                                    <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Основной адрес доставки</label>
                                    <input type="text" name="address_line1" class="form-control" value="<?= htmlspecialchars($user['address_line1'] ?? '') ?>" placeholder="Населенный пункт, улица, дом">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">Текущий пароль для подтверждения *</label>
                                    <input type="password" name="current_password" class="form-control" required placeholder="Введите ваш текущий пароль">
                                </div>
                                <button type="submit" name="update_personal" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">
                                    Сохранить данные
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Смена пароля -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">Изменить пароль</h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Текущий пароль *</label>
                                    <input type="password" name="current_password_for_pass" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Новый пароль (мин. 6 символов) *</label>
                                    <input type="password" name="new_password" class="form-control" required minlength="6">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">Повторите новый пароль *</label>
                                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                                </div>
                                <button type="submit" name="update_password" class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm">
                                    Обновить пароль
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';


// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php?redirect=profile');
    exit;
}


$userId = $_SESSION['user_id'];
$user = getUserById($userId);
$orders = getOrdersByUserId($userId);


// --- ЗАГРУЗКА ОТЗЫВОВ ---
$product_reviews = [];
$installer_reviews = [];
$site_reviews = [];


// Товары
$stmt = $pdo->prepare("SELECT pr.*, p.name as product_name, p.id as pid FROM product_reviews pr JOIN products p ON pr.product_id = p.id WHERE pr.user_id = ? ORDER BY pr.created_at DESC");
$stmt->execute([$userId]);
$product_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Мастера
try {
    $stmt = $pdo->prepare("SELECT ir.*, i.first_name, i.last_name, i.id as installer_id FROM installer_reviews ir JOIN installers i ON ir.installer_id = i.id WHERE ir.user_id = ? ORDER BY ir.created_at DESC");
    $stmt->execute([$userId]);
    $installer_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e){}


// Магазин
$stmt = $pdo->prepare("SELECT * FROM site_reviews WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$site_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);



$active_tab = 'orders';
$success_msg = ''; 
$error_msg = '';


// --- ОБРАБОТКА POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Удаление отзывов
    if (isset($_POST['delete_site_review'])) {
        $active_tab = 'reviews';
        $pdo->prepare("DELETE FROM site_reviews WHERE id = ? AND user_id = ?")->execute([(int)$_POST['review_id'], $userId]);
        $success_msg = "Отзыв о магазине удален.";
        // Обновляем список
        $stmt = $pdo->prepare("SELECT * FROM site_reviews WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $site_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    if (isset($_POST['delete_product_review'])) {
        $active_tab = 'reviews';
        $pdo->prepare("DELETE FROM product_reviews WHERE id = ? AND user_id = ?")->execute([(int)$_POST['review_id'], $userId]);
        $success_msg = "Отзыв о товаре удален.";
        // Обновляем
        $stmt = $pdo->prepare("SELECT pr.*, p.name as product_name, p.id as pid FROM product_reviews pr JOIN products p ON pr.product_id = p.id WHERE pr.user_id = ? ORDER BY pr.created_at DESC");
        $stmt->execute([$userId]);
        $product_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    if (isset($_POST['delete_installer_review'])) {
        $active_tab = 'reviews';
        $pdo->prepare("DELETE FROM installer_reviews WHERE id = ? AND user_id = ?")->execute([(int)$_POST['review_id'], $userId]);
        $success_msg = "Отзыв о мастере удален.";
        // Обновляем
        try {
            $stmt = $pdo->prepare("SELECT ir.*, i.first_name, i.last_name, i.id as installer_id FROM installer_reviews ir JOIN installers i ON ir.installer_id = i.id WHERE ir.user_id = ? ORDER BY ir.created_at DESC");
            $stmt->execute([$userId]);
            $installer_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e){}
    }


    // Смена ЛИЧНЫХ ДАННЫХ
    if (isset($_POST['update_personal'])) {
        $active_tab = 'settings';
        $current_password = $_POST['current_password'] ?? '';
        
        if (empty($current_password) || !password_verify($current_password, $user['password'])) {
            $error_msg = 'Ошибка: Неверный текущий пароль.';
        } else {
            // Обновляем только Имя, Фамилию, Телефон, Адрес (address_line1)
            $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, phone=?, address_line1=? WHERE id=?");
            if ($stmt->execute([
                trim($_POST['first_name']), trim($_POST['last_name']), trim($_POST['phone']),
                trim($_POST['address_line1']), $userId
            ])) {
                $success_msg = 'Личные данные обновлены.';
                $user = getUserById($userId);
            } else {
                $error_msg = 'Ошибка базы данных.';
            }
        }
    }


    // Смена ПАРОЛЯ
    if (isset($_POST['update_password'])) {
        $active_tab = 'settings';
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];
        $curr_pass = $_POST['current_password_for_pass'];


        if (!password_verify($curr_pass, $user['password'])) {
            $error_msg = 'Неверный текущий пароль.';
        } elseif (strlen($new_pass) < 6) {
            $error_msg = 'Новый пароль слишком короткий (мин. 6 символов).';
        } elseif ($new_pass !== $confirm_pass) {
            $error_msg = 'Новые пароли не совпадают.';
        } else {
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $userId]);
            $success_msg = 'Пароль успешно изменен.';
            $user = getUserById($userId); 
        }
    }
}


function getStatusLabel($status) {
    // Желто-черная тема, контрастный текст
    $map = [
        'pending'    => ['bg' => 'warning text-dark', 't' => 'Новый'], 
        'processing' => ['bg' => 'primary text-white', 't' => 'В работе'], 
        'confirmed'  => ['bg' => 'info text-dark', 't' => 'Подтвержден'],
        'shipped'    => ['bg' => 'info text-white', 't' => 'Отправлен'], 
        'completed'  => ['bg' => 'success text-white', 't' => 'Выполнен'], 
        'cancelled'  => ['bg' => 'danger text-white', 't' => 'Отменен']
    ];
    $s = $map[$status] ?? ['bg' => 'secondary text-white', 't' => $status];
    return "<span class='badge bg-{$s['bg']}'>{$s['t']}</span>";
}


$page_title = 'Личный кабинет — ' . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-4 py-lg-5 bg-light">
    <div class="container">
        
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Личный кабинет</li>
            </ol>
        </nav>

        <!-- Карточка пользователя / Шапка -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-dark text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-3 border-warning flex-shrink-0" style="width: 64px; height: 64px; font-size: 1.5rem; font-weight: 700;">
                        <?= mb_substr($user['first_name'] ?? '', 0, 1) . mb_substr($user['last_name'] ?? '', 0, 1) ?>
                    </div>
                    <div>
                        <h1 class="h4 mb-1 fw-bold text-dark"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></h1>
                        <span class="text-muted small"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user['email'] ?? '') ?></span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <div class="text-center px-3 py-2 bg-light rounded-3 border">
                        <div class="fw-bold fs-5 text-dark"><?= count($orders) ?></div>
                        <div class="small text-muted">Заказов</div>
                    </div>
                    <div class="text-center px-3 py-2 bg-light rounded-3 border">
                        <div class="fw-bold fs-5 text-dark"><?= count($product_reviews) + count($installer_reviews) + count($site_reviews) ?></div>
                        <div class="small text-muted">Отзывов</div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $success_msg ?>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error_msg ?>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Сайдбар меню -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white sticky-lg-top" style="top: 85px; z-index: 10;">
                    <div class="nav flex-column nav-pills gap-1" id="v-pills-tab" role="tablist">
                        <button class="nav-link text-start rounded-pill <?= $active_tab === 'orders' ? 'active bg-warning text-dark fw-bold' : 'text-dark' ?>" id="tab-orders" data-bs-toggle="pill" data-bs-target="#content-orders">
                            <i class="bi bi-box-seam me-2"></i> История заказов
                        </button>
                        <button class="nav-link text-start rounded-pill <?= $active_tab === 'reviews' ? 'active bg-warning text-dark fw-bold' : 'text-dark' ?>" id="tab-reviews" data-bs-toggle="pill" data-bs-target="#content-reviews">
                            <i class="bi bi-chat-left-text me-2"></i> Мои отзывы
                        </button>
                        <button class="nav-link text-start rounded-pill <?= $active_tab === 'settings' ? 'active bg-warning text-dark fw-bold' : 'text-dark' ?>" id="tab-settings" data-bs-toggle="pill" data-bs-target="#content-settings">
                            <i class="bi bi-gear me-2"></i> Настройки профиля
                        </button>
                        <hr class="my-2">
                        <a href="includes/logout.php" class="nav-link text-start text-danger rounded-pill">
                            <i class="bi bi-box-arrow-right me-2"></i> Выйти из аккаунта
                        </a>
                    </div>
                </div>
            </div>

            <!-- Контент вкладок -->
            <div class="col-lg-9">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <!-- 1. ЗАКАЗЫ -->
                    <div class="tab-pane fade <?= $active_tab === 'orders' ? 'show active' : '' ?>" id="content-orders">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="card-header bg-dark text-white py-3">
                                <h5 class="mb-0 fw-bold fs-6"><i class="bi bi-box-seam text-warning me-2"></i>История заказов</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($orders)): ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-inbox display-4 text-muted opacity-50 mb-3 d-block"></i>
                                        <p class="text-muted mb-0">У вас пока нет оформленных заказов.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-4 py-3">№ Заказа</th>
                                                    <th class="py-3">Дата</th>
                                                    <th class="py-3">Статус</th>
                                                    <th class="text-end pe-4 py-3">Сумма</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($orders as $o): ?>
                                                    <tr class="order-row border-bottom" data-id="<?= $o['id'] ?>" style="cursor: pointer;" title="Нажмите для просмотра деталей">
                                                        <td class="ps-4 fw-bold text-dark">#<?= $o['id'] ?></td>
                                                        <td class="text-muted"><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
                                                        <td><?= getStatusLabel($o['status']) ?></td>
                                                        <td class="text-end fw-bold text-dark pe-4"><?= number_format($o['total_price'], 0, '', ' ') ?> ₽</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 2. ОТЗЫВЫ -->
                    <div class="tab-pane fade <?= $active_tab === 'reviews' ? 'show active' : '' ?>" id="content-reviews">
                        <!-- Товары -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                            <div class="card-header bg-dark text-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-star-fill text-warning me-2"></i>Отзывы о товарах</h6>
                            </div>
                            <div class="card-body p-4">
                                <?php if(!$product_reviews): ?>
                                    <p class="text-muted small mb-0">Вы пока не оставляли отзывов о товарах.</p>
                                <?php endif; ?>
                                <?php foreach($product_reviews as $r): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark"><?= htmlspecialchars($r['product_name']) ?></strong>
                                            <form method="POST" onsubmit="return confirm('Удалить отзыв?')">
                                                <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                                <button name="delete_product_review" class="btn btn-sm btn-outline-danger border-0 p-1" title="Удалить"><i class="bi bi-trash3"></i></button>
                                            </form>
                                        </div>
                                        <div class="text-warning small mb-1"><?= str_repeat('★', $r['rating']) ?></div>
                                        <p class="mb-0 text-muted small"><?= htmlspecialchars($r['comment']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Мастера -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                            <div class="card-header bg-dark text-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-tools text-warning me-2"></i>Отзывы о мастерах</h6>
                            </div>
                            <div class="card-body p-4">
                                <?php if(!$installer_reviews): ?>
                                    <p class="text-muted small mb-0">Вы пока не оставляли отзывов о мастерах.</p>
                                <?php endif; ?>
                                <?php foreach($installer_reviews as $r): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark"><?= htmlspecialchars(($r['first_name']??'').' '.($r['last_name']??'')) ?></strong>
                                            <form method="POST" onsubmit="return confirm('Удалить отзыв?')">
                                                <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                                <button name="delete_installer_review" class="btn btn-sm btn-outline-danger border-0 p-1" title="Удалить"><i class="bi bi-trash3"></i></button>
                                            </form>
                                        </div>
                                        <div class="text-warning small mb-1"><?= str_repeat('★', $r['rating']) ?></div>
                                        <p class="mb-0 text-muted small"><?= htmlspecialchars($r['comment']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Магазин -->
                        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                            <div class="card-header bg-dark text-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-shop text-warning me-2"></i>Отзывы о магазине</h6>
                            </div>
                            <div class="card-body p-4">
                                <?php if(!$site_reviews): ?>
                                    <p class="text-muted small mb-0">Вы пока не оставляли отзывов о магазине.</p>
                                <?php endif; ?>
                                <?php foreach($site_reviews as $r): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="badge bg-warning text-dark">Отзыв о магазине</span>
                                            <form method="POST" onsubmit="return confirm('Удалить отзыв?')">
                                                <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                                                <button name="delete_site_review" class="btn btn-sm btn-outline-danger border-0 p-1" title="Удалить"><i class="bi bi-trash3"></i></button>
                                            </form>
                                        </div>
                                        <div class="text-warning small mb-1"><?= str_repeat('★', $r['rating']) ?></div>
                                        <p class="mb-0 text-muted small"><?= htmlspecialchars($r['comment']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 3. НАСТРОЙКИ -->
                    <div class="tab-pane fade <?= $active_tab === 'settings' ? 'show active' : '' ?>" id="content-settings">
                        <!-- Личные данные -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                            <div class="card-header bg-dark text-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-person-gear text-warning me-2"></i>Личные данные</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="profile.php" method="POST">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Имя</label>
                                            <input type="text" name="first_name" class="form-control bg-light" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Фамилия</label>
                                            <input type="text" name="last_name" class="form-control bg-light" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Телефон</label>
                                            <input type="tel" name="phone" id="phone_input" class="form-control bg-light" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                        </div>
                                        
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Адрес доставки</label>
                                            <input type="text" name="address_line1" class="form-control bg-light" value="<?= htmlspecialchars($user['address_line1'] ?? '') ?>" placeholder="Город, улица, дом, квартира">
                                        </div>
                                        
                                        <div class="col-12 mt-4 pt-3 border-top">
                                            <label class="form-label text-danger fw-bold small text-uppercase">Текущий пароль для подтверждения *</label>
                                            <input type="password" name="current_password" class="form-control bg-light" required placeholder="••••••••">
                                        </div>
                                        <div class="col-12 mt-3">
                                            <button type="submit" name="update_personal" class="btn btn-warning fw-bold px-4 rounded-pill shadow-sm">
                                                Сохранить изменения
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Смена пароля -->
                        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                            <div class="card-header bg-dark text-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-lock text-warning me-2"></i>Безопасность и пароль</h6>
                            </div>
                            <div class="card-body p-4">
                                <form action="profile.php" method="POST">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Новый пароль</label>
                                            <input type="password" name="new_password" class="form-control bg-light" required placeholder="••••••••">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase">Подтвердите новый пароль</label>
                                            <input type="password" name="confirm_password" class="form-control bg-light" required placeholder="••••••••">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-danger text-uppercase">Текущий пароль *</label>
                                            <input type="password" name="current_password_for_pass" class="form-control bg-light" required placeholder="••••••••">
                                        </div>
                                        <div class="col-12 mt-3">
                                            <button type="submit" name="update_password" class="btn btn-dark fw-bold px-4 rounded-pill shadow-sm">
                                                Обновить пароль
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Заказ #<span id="modalOrderId" class="text-warning"></span></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-white" id="orderModalBody"></div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="https://unpkg.com/imask"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if(document.getElementById('phone_input') && typeof IMask !== 'undefined') {
        IMask(document.getElementById('phone_input'), { mask: '+{7} (000) 000-00-00' });
    }
    const modalEl = document.getElementById('orderModal');
    if (modalEl) {
        const orderModal = new bootstrap.Modal(modalEl);
        document.querySelectorAll('.order-row').forEach(row => {
            row.addEventListener('click', function() {
                const id = this.dataset.id;
                document.getElementById('modalOrderId').textContent = id;
                document.getElementById('orderModalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-warning"></div></div>';
                orderModal.show();
                fetch('ajax/get_order_details.php?order_id=' + id).then(r=>r.json()).then(d=>{
                    if(d.success) {
                        let h = '<div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Товар</th><th class="text-center">Кол-во</th><th class="text-end">Цена</th></tr></thead><tbody>';
                        d.items.forEach(i => h+=`<tr><td><div class="fw-bold">${i.name}</div></td><td class="text-center">${i.quantity} шт.</td><td class="text-end fw-bold">${new Intl.NumberFormat('ru-RU').format(i.price)} ₽</td></tr>`);
                        h+='</tbody></table></div>';
                        document.getElementById('orderModalBody').innerHTML = h;
                    } else {
                        document.getElementById('orderModalBody').innerHTML = `<div class="alert alert-danger mb-0">${d.message}</div>`;
                    }
                });
            });
        });
    }
});
</script>

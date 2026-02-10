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


require_once __DIR__ . '/includes/header.php';
?>


<style>
    .profile-nav .nav-link { color: #495057; border-radius: 8px; font-weight: 500; padding: 12px 16px; margin-bottom: 4px; display: flex; align-items: center; }
    .profile-nav .nav-link:hover { background-color: #e9ecef; color: #212529; }
    .profile-nav .nav-link.active { background-color: #ffc107 !important; color: #212529 !important; font-weight: 600; }
    .profile-nav .nav-link i { font-size: 1.1em; margin-right: 10px; }
</style>


<main class="py-5 bg-light">
    <div class="container">
        
        <!-- Шапка -->
        <div class="d-flex align-items-center mb-4">
            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:64px;height:64px;font-size:24px">
                <?= mb_substr($user['first_name'], 0, 1) . mb_substr($user['last_name'], 0, 1) ?>
            </div>
            <div>
                <h2 class="h4 mb-0 fw-bold"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                <span class="text-muted small"><?= htmlspecialchars($user['email']) ?></span>
            </div>
        </div>


        <?php if ($success_msg): ?><div class="alert alert-success alert-dismissible fade show"><?= $success_msg ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-danger alert-dismissible fade show"><?= $error_msg ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>


        <div class="row g-4">
            <div class="col-lg-3">
                <!-- САЙДБАР (top: 100px) -->
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 900;">
                    <div class="card-body p-2">
                        <div class="nav flex-column nav-pills profile-nav" id="v-pills-tab" role="tablist">
                            <button class="nav-link text-start <?= $active_tab === 'orders' ? 'active' : '' ?>" id="tab-orders" data-bs-toggle="pill" data-bs-target="#content-orders"><i class="bi bi-box-seam"></i> История заказов</button>
                            <button class="nav-link text-start <?= $active_tab === 'reviews' ? 'active' : '' ?>" id="tab-reviews" data-bs-toggle="pill" data-bs-target="#content-reviews"><i class="bi bi-chat-left-text"></i> Мои отзывы</button>
                            <button class="nav-link text-start <?= $active_tab === 'settings' ? 'active' : '' ?>" id="tab-settings" data-bs-toggle="pill" data-bs-target="#content-settings"><i class="bi bi-gear"></i> Настройки</button>
                            <hr class="my-2">
                            <a href="includes/logout.php" class="nav-link text-start text-danger"><i class="bi bi-box-arrow-right"></i> Выйти</a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-9">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <!-- 1. ЗАКАЗЫ -->
                    <div class="tab-pane fade <?= $active_tab === 'orders' ? 'show active' : '' ?>" id="content-orders">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Ваши заказы</h5></div>
                            <div class="card-body">
                                <?php if (empty($orders)): ?>
                                    <p class="text-muted text-center py-4">Нет заказов.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead><tr><th>№</th><th>Дата</th><th>Статус</th><th class="text-end">Сумма</th></tr></thead>
                                            <tbody>
                                                <?php foreach ($orders as $o): ?>
                                                    <tr class="order-row" data-id="<?= $o['id'] ?>" style="cursor: pointer;">
                                                        <td class="fw-bold">#<?= $o['id'] ?></td>
                                                        <td><?= date('d.m.Y', strtotime($o['created_at'])) ?></td>
                                                        <td><?= getStatusLabel($o['status']) ?></td>
                                                        <td class="text-end fw-bold"><?= number_format($o['total_price'], 0, '', ' ') ?> ₽</td>
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
                        <div class="d-flex justify-content-between mb-4">
                            <h4 class="fw-bold mb-0">Мои отзывы</h4>
                        </div>
                        
                        <!-- Товары -->
                        <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white fw-bold text-primary">О товарах</div><div class="card-body">
                            <?php if(!$product_reviews): ?>Нет отзывов<?php endif; ?>
                            <?php foreach($product_reviews as $r): ?>
                                <div class="border-bottom pb-2 mb-2">
                                    <div class="d-flex justify-content-between"><strong><?= htmlspecialchars($r['product_name']) ?></strong><form method="POST" onsubmit="return confirm('Удалить?')"><input type="hidden" name="review_id" value="<?= $r['id'] ?>"><button name="delete_product_review" class="btn-close btn-sm"></button></form></div>
                                    <div class="text-warning small"><?= str_repeat('★', $r['rating']) ?></div><p class="mb-0 text-muted small"><?= htmlspecialchars($r['comment']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div></div>
                        
                        <!-- Мастера -->
                        <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white fw-bold text-success">О мастерах</div><div class="card-body">
                            <?php if(!$installer_reviews): ?>Нет отзывов<?php endif; ?>
                            <?php foreach($installer_reviews as $r): ?>
                                <div class="border-bottom pb-2 mb-2">
                                    <div class="d-flex justify-content-between"><strong><?= htmlspecialchars(($r['first_name']??'').' '.($r['last_name']??'')) ?></strong><form method="POST" onsubmit="return confirm('Удалить?')"><input type="hidden" name="review_id" value="<?= $r['id'] ?>"><button name="delete_installer_review" class="btn-close btn-sm"></button></form></div>
                                    <div class="text-warning small"><?= str_repeat('★', $r['rating']) ?></div><p class="mb-0 text-muted small"><?= htmlspecialchars($r['comment']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div></div>
                        
                        <!-- Магазин -->
                        <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white fw-bold text-info">О магазине</div><div class="card-body">
                            <?php if(!$site_reviews): ?>Нет отзывов<?php endif; ?>
                            <?php foreach($site_reviews as $r): ?>
                                <div class="border-bottom pb-2 mb-2">
                                    <div class="d-flex justify-content-between"><span class="badge bg-light text-dark border">Отзыв</span><form method="POST" onsubmit="return confirm('Удалить?')"><input type="hidden" name="review_id" value="<?= $r['id'] ?>"><button name="delete_site_review" class="btn-close btn-sm"></button></form></div>
                                    <div class="text-warning small"><?= str_repeat('★', $r['rating']) ?></div><p class="mb-0 text-muted small"><?= htmlspecialchars($r['comment']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div></div>
                    </div>


                    <!-- 3. НАСТРОЙКИ -->
                    <div class="tab-pane fade <?= $active_tab === 'settings' ? 'show active' : '' ?>" id="content-settings">
                        
                        <!-- Личные данные -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Личные данные</h5></div>
                            <div class="card-body">
                                <form action="profile.php" method="POST">
                                    <div class="row g-3">
                                        <div class="col-md-6"><label class="form-label small text-muted">Имя</label><input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required></div>
                                        <div class="col-md-6"><label class="form-label small text-muted">Фамилия</label><input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required></div>
                                        <div class="col-12"><label class="form-label small text-muted">Телефон</label><input type="tel" name="phone" id="phone_input" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>"></div>
                                        
                                        <!-- ИСПРАВЛЕННОЕ ПОЛЕ АДРЕСА: ГАРАНТИРУЕМ ПУСТОТУ ЕСЛИ НЕТ ДАННЫХ -->
                                        <div class="col-12"><label class="form-label small text-muted">Адрес доставки</label><input type="text" name="address_line1" class="form-control" value="<?= htmlspecialchars($user['address_line1'] ?? '') ?>" placeholder="Улица, дом, квартира"></div>
                                        
                                        <div class="col-12 mt-4"><label class="form-label text-danger fw-bold">Текущий пароль для подтверждения *</label><input type="password" name="current_password" class="form-control" required></div>
                                        <div class="col-12 mt-3"><button type="submit" name="update_personal" class="btn btn-warning fw-bold w-100">Сохранить данные</button></div>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <!-- Смена пароля -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Смена пароля</h5></div>
                            <div class="card-body">
                                <form action="profile.php" method="POST">
                                    <div class="mb-3"><label class="form-label small text-muted">Новый пароль</label><input type="password" name="new_password" class="form-control" required></div>
                                    <div class="mb-3"><label class="form-label small text-muted">Подтвердите новый пароль</label><input type="password" name="confirm_password" class="form-control" required></div>
                                    <div class="mb-3"><label class="form-label small text-muted text-danger">Текущий пароль *</label><input type="password" name="current_password_for_pass" class="form-control" required></div>
                                    <button type="submit" name="update_password" class="btn btn-dark w-100">Обновить пароль</button>
                                </form>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<div class="modal fade" id="orderModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-warning"><h5 class="modal-title">Заказ #<span id="modalOrderId"></span></h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-4" id="orderModalBody"></div></div></div></div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/imask"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if(document.getElementById('phone_input')) IMask(document.getElementById('phone_input'), { mask: '+{7} (000) 000-00-00' });
    const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
    document.querySelectorAll('.order-row').forEach(row => {
        row.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('modalOrderId').textContent = id;
            document.getElementById('orderModalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-warning"></div></div>';
            orderModal.show();
            fetch('ajax/get_order_details.php?order_id=' + id).then(r=>r.json()).then(d=>{
                if(d.success) {
                    let h = '<table class="table mb-0"><thead><tr><th>Товар</th><th>Кол</th><th class="text-end">Цена</th></tr></thead><tbody>';
                    d.items.forEach(i => h+=`<tr><td>${i.name}</td><td>${i.quantity}</td><td class="text-end">${i.price} ₽</td></tr>`);
                    h+='</tbody></table>';
                    document.getElementById('orderModalBody').innerHTML = h;
                } else document.getElementById('orderModalBody').innerHTML = d.message;
            });
        });
    });
});
</script>

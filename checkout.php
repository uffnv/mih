<?php
// checkout.php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$page_title = 'Оформление заказа';

// 1. ИСПРАВЛЕННЫЙ ЗАПРОС (БЕЗ JOIN-ов КАРТИНОК - НЕТ УЧЕТВЕРЕНИЯ)
$stmt = $pdo->prepare("
    SELECT c.quantity, c.product_id, p.name, p.price
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

// Получаем данные юзера
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

$full_name_value = trim(($user_info['first_name'] ?? '') . ' ' . ($user_info['last_name'] ?? ''));
$phone_value = $user_info['phone'] ?? '';
$email_value = $user_info['email'] ?? '';
$address_value = $user_info['address_line1'] ?? '';

$error_message = '';
$success_message = '';
$calculated_total = 0;

foreach ($cart_items as $item) {
    $calculated_total += $item['price'] * $item['quantity'];
}

// ОБРАБОТКА ФОРМЫ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $comment = trim($_POST['comment']);
    $delivery_method = $_POST['delivery_method'] ?? 'pickup';
    $payment_method = $_POST['payment_method'] ?? 'cash';
    
    $errors = [];
    if (empty($name)) $errors[] = 'Введите имя';
    
    $clean_phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($clean_phone) < 10) $errors[] = 'Введите корректный номер телефона.';

    if ($delivery_method === 'courier' && empty($address)) {
        $errors[] = 'Введите адрес доставки для курьера';
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (
                user_id, total_price, status, created_at, delivery_method, payment_method, 
                delivery_address, customer_name, customer_phone, customer_email, comment
            ) VALUES (?, ?, 'pending', NOW(), ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $user_id, $calculated_total, 
                $delivery_method, $payment_method, $address, 
                $name, $phone, $email, $comment
            ]);
            
            $order_id = $pdo->lastInsertId();

            $stmtInsertItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, product_name) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($cart_items as $item) {
                $stmtInsertItem->execute([
                    $order_id, 
                    $item['product_id'], 
                    $item['quantity'], 
                    $item['price'], 
                    $item['name']
                ]);
            }

            $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
            $pdo->commit();
            
            $success_message = "Заказ #$order_id успешно оформлен!";
            $cart_items = [];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Ошибка: " . $e->getMessage();
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="py-4 py-lg-5 bg-light">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php">Главная</a></li>
                <li class="breadcrumb-item"><a href="cart.php">Корзина</a></li>
                <li class="breadcrumb-item active" aria-current="page">Оформление заказа</li>
            </ol>
        </nav>

        <!-- Прогресс оформления -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between position-relative">
                    <div class="text-center flex-fill">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-1" style="width: 32px; height: 32px;">
                            <i class="bi bi-check2"></i>
                        </div>
                        <div class="small fw-bold text-muted">1. Корзина</div>
                    </div>
                    <div class="text-center flex-fill">
                        <div class="rounded-circle bg-warning text-dark fw-bold d-inline-flex align-items-center justify-content-center mb-1 shadow-sm" style="width: 32px; height: 32px;">
                            2
                        </div>
                        <div class="small fw-bold text-dark">2. Данные и доставка</div>
                    </div>
                    <div class="text-center flex-fill">
                        <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mb-1" style="width: 32px; height: 32px;">
                            3
                        </div>
                        <div class="small text-muted">3. Подтверждение</div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white my-4">
                <i class="bi bi-check-circle-fill display-2 text-success mb-3"></i>
                <h2 class="fw-bold text-dark"><?= htmlspecialchars($success_message) ?></h2>
                <p class="text-muted lead fs-6 mb-4">Менеджер свяжется с вами в ближайшее время для уточнения деталей доставки.</p>
                <div>
                    <a href="catalog.php" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Вернуться в каталог
                    </a>
                </div>
            </div>
        <?php elseif (empty($cart_items)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white my-4">
                <i class="bi bi-cart-x display-3 text-muted mb-3"></i>
                <h4 class="fw-bold">Корзина пуста</h4>
                <p class="text-muted mb-4">Для оформления заказа добавьте товары в корзину</p>
                <div>
                    <a href="catalog.php" class="btn btn-warning fw-bold rounded-pill px-4">В каталог</a>
                </div>
            </div>
        <?php else: ?>
        
            <?php if ($error_message): ?>
                <div class="alert alert-danger rounded-4 mb-4 shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error_message ?>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Форма оформления -->
                <div class="col-lg-8">
                    <form method="POST" id="checkout-form">
                        <!-- 1. Контакты -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                            <div class="card-header bg-dark text-white py-3">
                                <h5 class="mb-0 fw-bold fs-6">
                                    <i class="bi bi-person-lines-fill text-warning me-2"></i>1. Контактные данные
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Имя получателя *</label>
                                        <input type="text" name="name" class="form-control bg-light" value="<?= htmlspecialchars($full_name_value) ?>" placeholder="Иван Иванов" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Телефон *</label>
                                        <input type="tel" name="phone" id="phone" class="form-control bg-light" value="<?= htmlspecialchars($phone_value) ?>" placeholder="+7 (___) ___-__-__" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Email адрес</label>
                                        <input type="email" name="email" class="form-control bg-light" value="<?= htmlspecialchars($email_value) ?>" placeholder="name@example.com">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Доставка -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                            <div class="card-header bg-dark text-white py-3">
                                <h5 class="mb-0 fw-bold fs-6">
                                    <i class="bi bi-truck text-warning me-2"></i>2. Способ доставки
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 bg-light h-100">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="delivery_method" id="del1" value="pickup" checked onchange="toggleAddress(false)">
                                                <label class="form-check-label fw-bold d-block" for="del1">
                                                    Самовывоз (Бесплатно)
                                                    <span class="d-block small text-muted fw-normal">с. Майма (ул. Ленина, 1/1) или г. Горно-Алтайск (ТЦ «Кристалл»)</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 bg-light h-100">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="delivery_method" id="del2" value="courier" onchange="toggleAddress(true)">
                                                <label class="form-check-label fw-bold d-block" for="del2">
                                                    Курьерская доставка
                                                    <span class="d-block small text-muted fw-normal">Доставим до двери в удобное время</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="address-block" style="display:none;" class="mt-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Адрес доставки *</label>
                                    <input type="text" name="address" class="form-control bg-light" value="<?= htmlspecialchars($address_value) ?>" placeholder="Город, улица, дом, квартира">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Оплата -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                            <div class="card-header bg-dark text-white py-3">
                                <h5 class="mb-0 fw-bold fs-6">
                                    <i class="bi bi-credit-card text-warning me-2"></i>3. Способ оплаты
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 bg-light h-100">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" id="pay1" value="cash" checked>
                                                <label class="form-check-label fw-bold" for="pay1">
                                                    <i class="bi bi-cash me-1 text-success"></i> Наличными при получении
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 bg-light h-100">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" id="pay2" value="card">
                                                <label class="form-check-label fw-bold" for="pay2">
                                                    <i class="bi bi-credit-card-2-front me-1 text-primary"></i> Картой при получении
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Комментарий -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Комментарий к заказу</label>
                            <textarea name="comment" class="form-control bg-light" rows="2" placeholder="Пожелания по времени доставки, этаж, домофон..."></textarea>
                        </div>

                        <div class="mb-3 px-2 text-center text-muted small">
                            Нажимая «Подтвердить заказ», вы соглашаетесь на 
                            <a href="docs/personal_data_agreement.pdf" target="_blank" class="text-dark fw-bold text-decoration-underline">
                                обработку персональных данных
                            </a> в соответствии с 152-ФЗ
                        </div>

                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold py-3 rounded-pill shadow-sm fs-5">
                            <i class="bi bi-check2-circle me-1"></i> Подтвердить заказ
                        </button>
                    </form>
                </div>

                <!-- Сводка заказа -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white sticky-top overflow-hidden" style="top: 85px; z-index: 10;">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="mb-0 fw-bold fs-6">Ваш заказ</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($cart_items as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                    <div class="pe-2">
                                        <div class="fw-bold text-dark small text-truncate" style="max-width: 180px;"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="small text-muted"><?= $item['quantity'] ?> × <?= number_format($item['price'], 0, '.', ' ') ?> ₽</div>
                                    </div>
                                    <span class="fw-bold text-dark text-nowrap"><?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?> ₽</span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="p-4 bg-light border-top">
                                <div class="d-flex justify-content-between align-items-baseline fs-5 fw-bold text-dark">
                                    <span>Итого:</span>
                                    <span class="fs-4"><?= number_format($calculated_total, 0, '.', ' ') ?> ₽</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function toggleAddress(show) {
    const block = document.getElementById('address-block');
    if (block) block.style.display = show ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function (e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
            if (!x[2] && x[1] !== '') {
                e.target.value = x[1] === '7' || x[1] === '8' ? '+7 ' : '+7 ' + x[1];
            } else {
                e.target.value = !x[2] ? x[1] : '+7 (' + x[2] + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

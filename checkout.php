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

<main class="py-5 bg-light">
    <div class="container">
        <h1 class="mb-4">Оформление заказа</h1>

        <?php if ($success_message): ?>
            <div class="alert alert-success py-5 text-center">
                <i class="bi bi-check-circle display-1 text-success mb-3"></i>
                <h3><?= htmlspecialchars($success_message) ?></h3>
                <a href="catalog.php" class="btn btn-warning mt-3">Вернуться в магазин</a>
            </div>
        <?php elseif (empty($cart_items)): ?>
            <div class="alert alert-warning">Корзина пуста.</div>
        <?php else: ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <form method="POST" id="checkout-form">
                    <!-- 1. Контакты -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3"><h5 class="mb-0">1. Контактные данные</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Имя *</label>
                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($full_name_value) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Телефон *</label>
                                    <!-- Поле для ввода телефона с маской -->
                                    <input type="tel" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($phone_value) ?>" placeholder="+7 (___) ___-__-__" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email_value) ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Доставка -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3"><h5 class="mb-0">2. Доставка</h5></div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="delivery_method" id="del1" value="pickup" checked onchange="toggleAddress(false)">
                                <label class="form-check-label" for="del1">Самовывоз</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="delivery_method" id="del2" value="courier" onchange="toggleAddress(true)">
                                <label class="form-check-label" for="del2">Курьером</label>
                            </div>
                            <div id="address-block" style="display:none;">
                                <label class="form-label">Адрес доставки *</label>
                                <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($address_value) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Оплата -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3"><h5 class="mb-0">3. Оплата</h5></div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay1" value="cash" checked>
                                <label class="form-check-label" for="pay1">Наличными при получении</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay2" value="card">
                                <label class="form-check-label" for="pay2">Картой при получении</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Комментарий</label>
                        <textarea name="comment" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold py-3">Оформить заказ</button>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">Ваш заказ</h5></div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($cart_items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-bold text-truncate" style="max-width: 200px;"><?= htmlspecialchars($item['name']) ?></div>
                                    <div class="small text-muted"><?= $item['quantity'] ?> шт.</div>
                                </div>
                                <span class="fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, '.', ' ') ?> ₽</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="p-3 bg-light border-top">
                            <div class="d-flex justify-content-between fs-5 fw-bold">
                                <span>Итого:</span>
                                <span><?= number_format($calculated_total, 0, '.', ' ') ?> ₽</span>
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
// Переключение адреса
function toggleAddress(show) {
    document.getElementById('address-block').style.display = show ? 'block' : 'none';
}

// Простая маска телефона +7 (XXX) XXX-XX-XX
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    if(phoneInput){
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

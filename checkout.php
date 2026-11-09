<?php
// checkout.php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$user_id = $_SESSION['user_id'] ?? 0;
$page_title = 'Оформление заказа — Территория Тепла';

// Получаем корзину (поддержка авторизованного пользователя и гостя)
$cart_items = getCart($user_id);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

$calculated_total = 0;
foreach ($cart_items as $item) {
    $calculated_total += $item['price'] * $item['quantity'];
}

// Данные пользователя при наличии
$full_name_value = '';
$phone_value = '';
$email_value = '';
$address_value = '';

if ($user_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user_info) {
        $full_name_value = trim(($user_info['first_name'] ?? '') . ' ' . ($user_info['last_name'] ?? ''));
        $phone_value = $user_info['phone'] ?? '';
        $email_value = $user_info['email'] ?? '';
        $address_value = $user_info['address_line1'] ?? '';
    }
}

$error_message = '';
$success_message = '';

// ОБРАБОТКА ОФОРМЛЕНИЯ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    $delivery_method = $_POST['delivery_method'] ?? 'pickup';
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $consent_pd = isset($_POST['consent_pd']);
    $consent_offer = isset($_POST['consent_offer']);

    $errors = [];
    if (empty($name)) $errors[] = 'Укажите ваше имя.';
    
    $clean_phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($clean_phone) < 10) $errors[] = 'Введите корректный номер телефона (не менее 10 цифр).';

    if ($delivery_method === 'courier' && empty($address)) {
        $errors[] = 'Укажите адрес доставки для курьера.';
    }

    if (!$consent_pd) {
        $errors[] = 'Необходимо согласие на обработку персональных данных (152-ФЗ).';
    }
    if (!$consent_offer) {
        $errors[] = 'Необходимо согласие с условиями Публичной оферты.';
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Если гость - привязываем к существующему пользователю или создаем клиента
            $order_user_id = $user_id;
            if ($order_user_id == 0) {
                // Ищем по телефону или email
                $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE phone = ? OR (email != '' AND email = ?) LIMIT 1");
                $stmtCheck->execute([$phone, $email]);
                $existing_user = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                
                if ($existing_user) {
                    $order_user_id = (int)$existing_user['id'];
                } else {
                    // Создаем гостевую учетную запись
                    $gen_username = 'client_' . substr($clean_phone, -6) . '_' . rand(100, 999);
                    $dummy_pass = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                    $insUser = $pdo->prepare("
                        INSERT INTO users (username, password, first_name, phone, email, role, created_at)
                        VALUES (?, ?, ?, ?, ?, 'customer', NOW())
                    ");
                    $insUser->execute([$gen_username, $dummy_pass, $name, $phone, $email]);
                    $order_user_id = (int)$pdo->lastInsertId();
                }
            }

            if ($delivery_method === 'pickup') {
                $address = 'Самовывоз: с. Майма, ул. Энергетиков, д. 24';
            }

            $stmt = $pdo->prepare("INSERT INTO orders (
                user_id, total_price, status, created_at, delivery_method, payment_method, 
                delivery_address, customer_name, customer_phone, customer_email, comment
            ) VALUES (?, ?, 'pending', NOW(), ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $order_user_id, $calculated_total, 
                $delivery_method, $payment_method, $address, 
                $name, $phone, $email, $comment
            ]);
            
            $order_id = $pdo->lastInsertId();

            $stmtInsertItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, product_name) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($cart_items as $item) {
                $stmtInsertItem->execute([
                    $order_id, 
                    $item['product_id'] ?? $item['id'], 
                    $item['quantity'], 
                    $item['price'], 
                    $item['name']
                ]);
            }

            // Очищаем корзину в БД и сессии
            if ($user_id > 0) {
                $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
            }
            if (isset($_SESSION['guest_cart'])) {
                unset($_SESSION['guest_cart']);
            }

            $pdo->commit();
            
            $success_message = "Заказ #$order_id успешно оформлен!";
            $cart_items = [];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Ошибка при сохранении заказа: " . $e->getMessage();
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="py-4 py-lg-5" style="background-color: var(--bg-page, #f8fafc);">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item"><a href="cart.php" class="text-decoration-none text-muted">Корзина</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Оформление заказа</li>
            </ol>
        </nav>

        <!-- Шаги оформления -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between position-relative">
                    <div class="text-center flex-fill">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-1 shadow-sm" style="width: 34px; height: 34px;">
                            <i class="bi bi-check2"></i>
                        </div>
                        <div class="small fw-semibold text-muted">1. Корзина</div>
                    </div>
                    <div class="text-center flex-fill">
                        <div class="rounded-circle bg-warning text-dark fw-bold d-inline-flex align-items-center justify-content-center mb-1 shadow-sm" style="width: 34px; height: 34px;">
                            2
                        </div>
                        <div class="small fw-bold text-dark">2. Данные и доставка</div>
                    </div>
                    <div class="text-center flex-fill">
                        <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mb-1" style="width: 34px; height: 34px;">
                            3
                        </div>
                        <div class="small text-muted">3. Подтверждение</div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($success_message): ?>
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white my-4">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill display-2 text-success"></i>
                </div>
                <h2 class="fw-bold text-dark mb-2"><?= htmlspecialchars($success_message) ?></h2>
                <p class="text-muted lead fs-6 mb-4">Спасибо за заказ! Наш специалист свяжется с вами по указанному телефону для подтверждения наличия и деталей получения.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="catalog.php" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Вернуться в каталог
                    </a>
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
                            <div class="card-header py-3" style="background: var(--dark-main, #181c20); color: #fff;">
                                <h5 class="mb-0 fw-bold fs-6">
                                    <i class="bi bi-person-lines-fill text-warning me-2"></i> 1. Контактные данные получателя
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">ФИО получателя *</label>
                                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($full_name_value) ?>" placeholder="Иван Иванов" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Телефон *</label>
                                        <input type="tel" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($phone_value) ?>" placeholder="+7 (___) ___-__-__" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase">Email адрес (для чека)</label>
                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email_value) ?>" placeholder="name@example.com">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Доставка -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                            <div class="card-header py-3" style="background: var(--dark-main, #181c20); color: #fff;">
                                <h5 class="mb-0 fw-bold fs-6">
                                    <i class="bi bi-truck text-warning me-2"></i> 2. Способ получения
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
                                                    <span class="d-block small text-muted fw-normal mt-1">
                                                        <i class="bi bi-geo-alt-fill text-warning me-1"></i> с. Майма, ул. Энергетиков, д. 24
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 bg-light h-100">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="delivery_method" id="del2" value="courier" onchange="toggleAddress(true)">
                                                <label class="form-check-label fw-bold d-block" for="del2">
                                                    Доставка курьером / ТК
                                                    <span class="d-block small text-muted fw-normal mt-1">
                                                        По Республике Алтай и Алтайскому краю
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="address-block" style="display:none;" class="mt-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Адрес доставки *</label>
                                    <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($address_value) ?>" placeholder="Населенный пункт, улица, номер дома">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Оплата -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
                            <div class="card-header py-3" style="background: var(--dark-main, #181c20); color: #fff;">
                                <h5 class="mb-0 fw-bold fs-6">
                                    <i class="bi bi-credit-card text-warning me-2"></i> 3. Способ оплаты
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 bg-light h-100">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" id="pay1" value="cash" checked>
                                                <label class="form-check-label fw-bold d-block" for="pay1">
                                                    При получении
                                                    <span class="d-block small text-muted fw-normal mt-1">Оплата наличными или картой в магазине/курьеру</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 bg-light h-100">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" id="pay2" value="card">
                                                <label class="form-check-label fw-bold d-block" for="pay2">
                                                    Безналичный расчет
                                                    <span class="d-block small text-muted fw-normal mt-1">Выставление счета для физ. и юр. лиц</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Комментарий к заказу</label>
                                    <textarea name="comment" class="form-control" rows="2" placeholder="Удобное время звонка, особенности подъезда..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Юридические согласия (152-ФЗ и Оферта) -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="consent_pd" id="consent_pd" required checked>
                                <label class="form-check-label small text-muted" for="consent_pd">
                                    Я согласен на <a href="terms.php#privacy" target="_blank" class="text-dark fw-semibold">обработку персональных данных</a> в соответствии с Федеральным законом № 152-ФЗ. *
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="consent_offer" id="consent_offer" required checked>
                                <label class="form-check-label small text-muted" for="consent_offer">
                                    Я ознакомлен и согласен с условиями <a href="terms.php" target="_blank" class="text-dark fw-semibold">Публичной оферты</a> и правилами возврата товара надлежащего качества в течение 7 дней (ст. 26.1 ЗОЗПП). *
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-pill shadow-sm fs-5">
                            Подтвердить и оформить заказ <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </form>
                </div>

                <!-- Правая колонка: Сводка заказа -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 85px; z-index: 10;">
                        <h5 class="fw-bold mb-3 text-dark pb-2 border-bottom">Состав заказа</h5>
                        
                        <div class="order-items-list mb-3" style="max-height: 280px; overflow-y: auto;">
                            <?php foreach ($cart_items as $ci): 
                                $ciTotal = $ci['price'] * $ci['quantity'];
                            ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 small pb-2 border-bottom border-light">
                                    <div class="pe-2">
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 190px;">
                                            <?= htmlspecialchars($ci['name']) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <?= $ci['quantity'] ?> × <?= number_format($ci['price'], 0, '.', ' ') ?> ₽
                                        </div>
                                    </div>
                                    <div class="fw-bold text-nowrap text-dark">
                                        <?= number_format($ciTotal, 0, '.', ' ') ?> ₽
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex justify-content-between text-muted mb-2 small">
                            <span>Товаров:</span>
                            <span class="fw-semibold text-dark"><?= count($cart_items) ?> поз.</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mb-3 small">
                            <span>Доставка:</span>
                            <span class="text-success fw-medium" id="delivery-price-text">Бесплатно (самовывоз)</span>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <span class="fs-5 fw-bold text-dark">Итого:</span>
                            <span class="fs-3 fw-bold text-dark">
                                <?= number_format($calculated_total, 0, '.', ' ') ?> ₽
                            </span>
                        </div>

                        <div class="p-3 bg-light rounded-3 small text-muted">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-geo-alt-fill text-warning"></i>
                                <span><strong>Самовывоз:</strong> с. Майма, ул. Энергетиков, 24</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-telephone-fill text-warning"></i>
                                <span><strong>Справка:</strong> +7 (913) 691-44-43</span>
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
    const delText = document.getElementById('delivery-price-text');
    if (block) {
        block.style.display = show ? 'block' : 'none';
        const input = block.querySelector('input');
        if (input) input.required = show;
    }
    if (delText) {
        delText.innerText = show ? 'По тарифам ТК' : 'Бесплатно (самовывоз)';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Если уже вошел
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}

$redirect_target = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';
    
    // Генерируем username из email
    $username_base = explode('@', $email)[0];
    $username = substr($username_base, 0, 45);

    $agree_personal_data = isset($_POST['agree_personal_data']);

    if (!$first_name || !$last_name || !$email || !$phone || !$password) {
        $error = 'Все поля обязательны для заполнения!';
    } elseif (!$agree_personal_data) {
        $error = 'Необходимо дать согласие на обработку персональных данных.';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают!';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов.';
    } else {
        // Регистрация (всегда как customer)
        $result = registerUser($username, $email, $password, $first_name, $last_name, $phone, 'customer', $agree_personal_data);
        
        if ($result['success']) {
            // Автоматический вход после регистрации
            $user = loginUser($email, $password); // Тут используем стандартную функцию
            if ($user) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
                
                // Перенос корзины
                if (!empty($_SESSION['guest_cart'])) {
                    foreach ($_SESSION['guest_cart'] as $p_id => $qty) {
                        addToCart($user['id'], $p_id, $qty);
                    }
                    unset($_SESSION['guest_cart']);
                }

                if ($redirect_target === 'checkout') {
                    header("Location: checkout.php");
                } else {
                    header("Location: profile.php");
                }
                exit;
            }
            // На случай если авто-вход не сработал
            header("Location: login.php?registered=1");
            exit;
        } else {
            $error = $result['error'];
        }
    }
}

$page_title = "Регистрация";
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                
                <div class="card auth-card border-0 shadow-lg rounded-4 overflow-hidden mb-5">
                    <div class="card-header text-center py-4 bg-dark text-white border-0">
                        <h3 class="mb-0 fw-bold text-warning">Регистрация</h3>
                        <p class="mb-0 opacity-75 small">Создайте аккаунт для покупок</p>
                    </div>
                    
                    <div class="card-body p-4 p-md-5 bg-white">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger mb-4"><?= htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Имя *</label>
                                    <input type="text" name="first_name" class="form-control bg-light" placeholder="Иван" required value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Фамилия *</label>
                                    <input type="text" name="last_name" class="form-control bg-light" placeholder="Иванов" required value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Телефон *</label>
                                <input type="tel" name="phone" id="phone" class="form-control bg-light" placeholder="+7 (___) ___-__-__" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Email адрес *</label>
                                <input type="email" name="email" class="form-control bg-light" placeholder="name@example.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Пароль *</label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="reg-password" class="form-control bg-light" placeholder="Мин. 6 символов" required>
                                        <button class="btn btn-light border toggle-password-btn" type="button" data-target="reg-password" title="Показать">
                                            <i class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Повтор *</label>
                                    <div class="input-group">
                                        <input type="password" name="confirm_password" id="reg-confirm" class="form-control bg-light" placeholder="Повторите пароль" required>
                                        <button class="btn btn-light border toggle-password-btn" type="button" data-target="reg-confirm" title="Показать">
                                            <i class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Согласие на обработку персональных данных -->
                            <div class="mb-4 p-3 bg-light rounded-3 border">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="agree_personal_data" id="agree_personal_data" required
                                        <?= isset($_POST['agree_personal_data']) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="agree_personal_data">
                                        Я даю согласие на 
                                        <a href="docs/personal_data_agreement.pdf" target="_blank" class="text-warning fw-bold text-decoration-none">
                                            <i class="bi bi-file-earmark-pdf"></i> обработку персональных данных
                                        </a>
                                        в соответствии с Федеральным законом №&nbsp;152-ФЗ <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-pill shadow-sm">Создать аккаунт</button>
                            </div>
                        </form>

                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="text-muted mb-0 small">
                                Уже есть аккаунт? 
                                <a href="login.php<?= $redirect_target ? '?redirect='.$redirect_target : '' ?>" class="text-decoration-none fw-bold text-dark hover-warning">Войти</a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Блок преимуществ -->
                <div class="row text-center g-4">
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="fs-1 mb-2">🚀</div>
                            <h6 class="fw-bold">Быстро</h6>
                            <p class="small text-muted mb-0">Заказ в пару кликов</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="fs-1 mb-2">📦</div>
                            <h6 class="fw-bold">Удобно</h6>
                            <p class="small text-muted mb-0">История заказов</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <div class="fs-1 mb-2">🎁</div>
                            <h6 class="fw-bold">Выгодно</h6>
                            <p class="small text-muted mb-0">Бонусная система</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="https://unpkg.com/imask"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    if (phoneInput && typeof IMask !== 'undefined') {
        IMask(phoneInput, { mask: '+{7} (000) 000-00-00' });
    }
});
</script>

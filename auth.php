<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// --- ЛОГИКА (без изменений) ---
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
        exit;
    }
    if (isset($_GET['redirect']) && $_GET['redirect'] === 'checkout') {
        header("Location: checkout.php");
        exit;
    }
    header("Location: profile.php");
    exit;
}

$redirect_target = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : '';
$redirect_param = $redirect_target ? "&redirect=$redirect_target" : '';
$mode = isset($_GET['mode']) && $_GET['mode'] === 'register' ? 'register' : 'login';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // === ВХОД ===
    if (isset($_POST['login'])) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (!$email || !$password) {
            $error = 'Введите email и пароль.';
        } else {
            $user = loginUser($email, $password);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
                
                if ($user['role'] === 'admin') {
                    header("Location: admin/index.php");
                    exit;
                }
                
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
            } else {
                $error = 'Неверный email или пароль.';
            }
        }
    }
    // === РЕГИСТРАЦИЯ ===
    elseif (isset($_POST['register'])) {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $phone      = trim($_POST['phone'] ?? '');
        $password   = $_POST['password'] ?? '';
        $confirm    = $_POST['confirm_password'] ?? '';
        
        $username_base = explode('@', $email)[0];
        $username = substr($username_base, 0, 45);

        if (!$first_name || !$last_name || !$email || !$phone || !$password) {
            $error = 'Все поля обязательны для заполнения!';
        } 
        elseif ($password !== $confirm) {
            $error = 'Пароли не совпадают!';
        } 
        elseif (strlen($password) < 6) {
            $error = 'Пароль должен содержать минимум 6 символов.';
        } 
        else {
            $result = registerUser($username, $email, $password, $first_name, $last_name, $phone, 'customer');
            
            if ($result['success']) {
                $user = loginUser($email, $password);
                if ($user) {
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role']     = $user['role'];
                    
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
                $success = 'Регистрация прошла успешно! Теперь вы можете войти.';
                $mode = 'login';
            } else {
                $error = $result['error'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $mode === 'login' ? 'Вход' : 'Регистрация'; ?> | Печи & Котлы</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- === ДОБАВЛЕНО: ИКОНКА === -->
    <link rel="icon" href="images/favicon.png" type="image/png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://unpkg.com/imask"></script>
    <style>
        body { background-color: #f8f9fa; }
        .auth-card { border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .auth-header { background: linear-gradient(135deg, #ffc107 0%, #ffdb4d 100%); padding: 2rem; text-align: center; border: none; }
        .auth-title { font-weight: 700; color: #212529; margin: 0; letter-spacing: -0.5px; }
        .btn-auth { background-color: #212529; color: #fff; border-radius: 10px; padding: 12px; font-weight: 600; transition: all 0.3s ease; }
        .btn-auth:hover { background-color: #343a40; transform: translateY(-2px); }
        .form-control { padding: 12px 15px; border-radius: 8px; border: 1px solid #dee2e6; background-color: #f8f9fa; }
        .form-control:focus { background-color: #fff; box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.15); border-color: #ffc107; }
        .benefit-icon { font-size: 2.5rem; margin-bottom: 1rem; }
        .benefit-card { border: none; background: transparent; transition: transform 0.3s; }
        .benefit-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                
                <!-- Карточка авторизации -->
                <div class="card auth-card mb-5">
                    <div class="card-header auth-header">
                        <h2 class="auth-title h3">
                            <?= $mode === 'login' ? 'Вход в кабинет' : 'Регистрация'; ?>
                        </h2>
                        <p class="mb-0 mt-2 text-dark opacity-75 small">
                            <?= $mode === 'login' ? 'С возвращением! Введите данные для входа.' : 'Заполните форму, чтобы создать аккаунт.'; ?>
                        </p>
                    </div>
                    
                    <div class="card-body p-4 p-md-5 bg-white">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                <div><?= htmlspecialchars($error); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <div><?= htmlspecialchars($success); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($mode === 'login'): ?>
                            <!-- ФОРМА ВХОДА -->
                            <form action="auth.php?mode=login<?= $redirect_param; ?>" method="POST">
                                <div class="mb-3">
                                    <label for="login_email" class="form-label fw-bold small text-uppercase text-muted">Email адрес</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" id="login_email" class="form-control border-start-0" placeholder="name@example.com" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="login_password" class="form-label fw-bold small text-uppercase text-muted">Пароль</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="password" id="login_password" class="form-control border-start-0" placeholder="••••••" required>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="login" class="btn btn-auth btn-lg">Войти</button>
                                </div>
                            </form>
                            
                            <div class="text-center mt-4 pt-3 border-top">
                                <p class="text-muted mb-0">Нет аккаунта? <a href="auth.php?mode=register<?= $redirect_param; ?>" class="text-decoration-none fw-bold text-dark">Зарегистрироваться</a></p>
                            </div>

                        <?php else: ?>
                            <!-- ФОРМА РЕГИСТРАЦИИ -->
                            <form action="auth.php?mode=register<?= $redirect_param; ?>" method="POST">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label fw-bold small text-uppercase text-muted">Имя *</label>
                                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Иван" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label fw-bold small text-uppercase text-muted">Фамилия *</label>
                                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Иванов" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label fw-bold small text-uppercase text-muted">Телефон *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone"></i></span>
                                        <input type="tel" name="phone" id="phone" class="form-control border-start-0" placeholder="+7 (___) ___-__-__" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="reg_email" class="form-label fw-bold small text-uppercase text-muted">Email адрес *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" id="reg_email" class="form-control border-start-0" placeholder="name@example.com" required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="reg_password" class="form-label fw-bold small text-uppercase text-muted">Пароль *</label>
                                        <input type="password" name="password" id="reg_password" class="form-control" placeholder="Мин. 6 символов" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label fw-bold small text-uppercase text-muted">Повтор *</label>
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Повторите пароль" required>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" name="register" class="btn btn-auth btn-lg">Создать аккаунт</button>
                                </div>
                            </form>

                            <div class="text-center mt-4 pt-3 border-top">
                                <p class="text-muted mb-0">Уже зарегистрированы? <a href="auth.php?mode=login<?= $redirect_param; ?>" class="text-decoration-none fw-bold text-dark">Войти</a></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Блок преимуществ -->
                <div class="row text-center g-4 mt-2">
                    <div class="col-12 text-center mb-2">
                        <h5 class="fw-bold text-uppercase text-muted ls-1">Почему стоит зарегистрироваться?</h5>
                    </div>
                    <div class="col-md-4">
                        <div class="benefit-card">
                            <div class="benefit-icon">🚀</div>
                            <h6 class="fw-bold">Быстро</h6>
                            <p class="small text-muted">Заказ в пару кликов</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="benefit-card">
                            <div class="benefit-icon">📦</div>
                            <h6 class="fw-bold">Удобно</h6>
                            <p class="small text-muted">История всех покупок</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="benefit-card">
                            <div class="benefit-icon">🎁</div>
                            <h6 class="fw-bold">Выгодно</h6>
                            <p class="small text-muted">Бонусы и скидки</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    if (phoneInput && typeof IMask !== 'undefined') {
        IMask(phoneInput, { mask: '+{7} (000) 000-00-00' });
    }
});
</script>
</body>
</html>

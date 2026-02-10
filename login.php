<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Если пользователь уже авторизован — перенаправляем
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/index.php");
    } elseif ($_SESSION['role'] === 'installer') {
        header("Location: installers/index.php");
    } else {
        header("Location: profile.php");
    }
    exit;
}

$redirect_target = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? ''); // В поле логин может быть email или username (для монтажников)
    $password = $_POST['password'] ?? '';
    
    if (!$email || !$password) {
        $error = 'Введите email/логин и пароль.';
    } else {
        // Функция loginUser должна уметь искать и по email, и по username
        // Если ваша loginUser ищет только по email, нужно её доработать или использовать костыль ниже
        
        // Попытка входа
        $user = null;
        
        // 1. Пробуем найти по email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $found = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 2. Если не нашли по email, пробуем по username (для монтажников и админов)
        if (!$found) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$email]);
            $found = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Проверка пароля
        if ($found && password_verify($password, $found['password'])) {
            $user = $found;
        }

        if ($user) {
            // Успешный вход
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            
            // Логика переноса гостевой корзины
            if (!empty($_SESSION['guest_cart'])) {
                foreach ($_SESSION['guest_cart'] as $p_id => $qty) {
                    addToCart($user['id'], $p_id, $qty);
                }
                unset($_SESSION['guest_cart']);
            }
            
            // РЕДИРЕКТЫ ПО РОЛЯМ
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } elseif ($user['role'] === 'installer') {
                header("Location: installers/index.php");
            } else {
                if ($redirect_target === 'checkout') {
                    header("Location: checkout.php");
                } else {
                    header("Location: profile.php");
                }
            }
            exit;
        } else {
            $error = 'Неверные учетные данные.';
        }
    }
}

$page_title = "Вход в кабинет";
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card auth-card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header text-center py-4 bg-warning bg-gradient border-0">
                        <h3 class="mb-0 fw-bold text-dark">Вход</h3>
                        <p class="mb-0 text-dark opacity-75 small">Доступ к личному кабинету</p>
                    </div>
                    <div class="card-body p-4 p-md-5 bg-white">
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div><?= htmlspecialchars($error); ?></div>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold small text-muted text-uppercase">Email или Логин</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                    <input type="text" name="email" id="email" class="form-control border-start-0 bg-light" placeholder="Введите email" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between">
                                    <label for="password" class="form-label fw-bold small text-muted text-uppercase">Пароль</label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control border-start-0 bg-light" placeholder="••••••" required>
                                </div>
                            </div>
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-dark btn-lg fw-bold shadow-sm">Войти</button>
                            </div>
                        </form>

                        <div class="text-center">
                            <p class="text-muted mb-0">Нет аккаунта? <a href="register.php<?= $redirect_target ? '?redirect='.$redirect_target : '' ?>" class="text-decoration-none fw-bold text-warning">Зарегистрироваться</a></p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-muted small">
                    <p>Проблемы со входом? Свяжитесь с поддержкой.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

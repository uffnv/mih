<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
startSecureAuthSession();

// Если пользователь уже авторизован — перенаправляем
if (isset($_SESSION['user_id'])) {
    if (in_array($_SESSION['role'] ?? '', ['admin', 'moderator'], true)) {
        header("Location: admin/index.php");
    } elseif (($_SESSION['role'] ?? '') === 'installer') {
        header("Location: installers/index.php");
    } else {
        header("Location: profile.php");
    }
    exit;
}

$redirect_target = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
$allowed_redirects = ['checkout', 'profile'];
$redirect_safe = in_array($redirect_target, $allowed_redirects, true) ? $redirect_target : '';
$error = '';
$notice = '';

if (isset($_GET['timeout'])) {
    $notice = 'В целях безопасности сессия администратора завершена из-за бездействия (15 минут). Введите пароль повторно.';
} elseif (isset($_GET['logged_out'])) {
    $notice = 'Вы успешно вышли из учетной записи.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Сессия формы устарела. Обновите страницу и попробуйте снова.';
    } else {
        $loginCheck = validateLoginIdentifier($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if (!$loginCheck['valid']) {
            $error = $loginCheck['error'];
        } elseif (strlen($password) < 1 || strlen($password) > 128) {
            $error = 'Введите корректный пароль.';
        } else {
            $loginValue = $loginCheck['value'];
            $user = null;

            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$loginValue]);
            $found = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$found) {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
                $stmt->execute([$loginValue]);
                $found = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if ($found && (int)($found['is_active'] ?? 1) !== 1) {
                $error = 'Учетная запись заблокирована. Обратитесь к администратору.';
            } elseif ($found && password_verify($password, $found['password'])) {
                $user = $found;
            }

            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                if (in_array($user['role'], ['admin', 'moderator'], true)) {
                    initAdminSessionMetadata();
                }

                // Логика переноса гостевой корзины
                if (!empty($_SESSION['guest_cart'])) {
                    foreach ($_SESSION['guest_cart'] as $p_id => $qty) {
                        addToCart($user['id'], $p_id, $qty);
                    }
                    unset($_SESSION['guest_cart']);
                }

                if (in_array($user['role'], ['admin', 'moderator'], true)) {
                    header("Location: admin/index.php");
                } elseif ($user['role'] === 'installer') {
                    header("Location: installers/index.php");
                } else {
                    if ($redirect_safe === 'checkout') {
                        header("Location: checkout.php");
                    } else {
                        header("Location: profile.php");
                    }
                }
                exit;
            } elseif (!$error) {
                $error = 'Неверный логин или пароль.';
            }
        }
    }
}

$page_title = "Вход в кабинет — Территория Тепла";
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-4 py-md-5" style="background-color: var(--bg-page, #f8fafc);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                <div class="card auth-card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header text-center py-4 text-white border-0">
                        <span class="badge bg-warning text-dark text-uppercase fw-bold px-3 py-1 mb-2" style="font-size: 0.72rem;">Личный доступ</span>
                        <h1 class="h3 mb-1 fw-bold text-white">Вход в кабинет</h1>
                        <p class="mb-0 text-white-50 small">Покупателям, монтажникам и администраторам</p>
                    </div>
                    <div class="card-body p-4 p-md-5 bg-white">

                        <?php if ($notice): ?>
                            <div class="alert alert-warning d-flex align-items-start gap-2 rounded-3 mb-4 small border-0" style="background: #FFF9D6; color: #5c4900;" role="status">
                                <i class="bi bi-clock-history fs-5 flex-shrink-0"></i>
                                <div><?= htmlspecialchars($notice); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger d-flex align-items-start gap-2 rounded-3 mb-4 small" role="alert">
                                <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                                <div><?= htmlspecialchars($error); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['registered'])): ?>
                            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4 small">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                                <div>Регистрация успешна! Теперь вы можете войти.</div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" novalidate data-auth-form="login">
                            <?= getCsrfField(); ?>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold small text-muted text-uppercase">Email или Логин</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text"
                                           name="email"
                                           id="email"
                                           class="form-control border-start-0 bg-light"
                                           placeholder="name@example.ru или логин"
                                           autocomplete="username"
                                           inputmode="email"
                                           minlength="3"
                                           maxlength="100"
                                           pattern="[A-Za-z0-9@._+\-]{3,100}"
                                           data-rule="login-id"
                                           required
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                </div>
                                <span class="form-hint">Латинские буквы, цифры и символы @ . _ - (без пробелов)</span>
                                <div class="field-error-text"></div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold small text-muted text-uppercase">Пароль</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="form-control border-start-0 border-end-0 bg-light"
                                           placeholder="••••••••"
                                           autocomplete="current-password"
                                           maxlength="128"
                                           data-rule="password-login"
                                           required>
                                    <button class="btn btn-light border border-start-0 toggle-password-btn" type="button" data-target="password" title="Показать пароль" aria-label="Показать пароль">
                                        <i class="bi bi-eye text-muted"></i>
                                    </button>
                                </div>
                                <div class="caps-warning"></div>
                                <div class="field-error-text"></div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-pill shadow-sm">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Войти
                                </button>
                            </div>
                        </form>

                        <div class="text-center pt-3 border-top">
                            <p class="text-muted mb-0 small">
                                Нет аккаунта?
                                <a href="register.php<?= $redirect_safe ? '?redirect=' . urlencode($redirect_safe) : '' ?>" class="text-decoration-none fw-bold text-dark hover-warning ms-1">
                                    Зарегистрироваться
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

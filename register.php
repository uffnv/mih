<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
startSecureAuthSession();

// Если уже вошел
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}

$redirect_target = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
$redirect_safe = in_array($redirect_target, ['checkout', 'profile'], true) ? $redirect_target : '';
$error = '';

$formValues = [
    'first_name' => '',
    'last_name'  => '',
    'phone'      => '',
    'email'      => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formValues['first_name'] = trim($_POST['first_name'] ?? '');
    $formValues['last_name']  = trim($_POST['last_name'] ?? '');
    $formValues['phone']      = trim($_POST['phone'] ?? '');
    $formValues['email']      = trim($_POST['email'] ?? '');

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Сессия формы устарела. Обновите страницу и отправьте форму заново.';
    } else {
        $fnCheck    = validateHumanName($formValues['first_name'], 'Имя');
        $lnCheck    = validateHumanName($formValues['last_name'], 'Фамилия');
        $phoneCheck = normalizeAndValidatePhone($formValues['phone']);
        $emailCheck = validateEmailStrict($formValues['email']);
        $passRaw    = (string)($_POST['password'] ?? '');
        $confirmRaw = (string)($_POST['confirm_password'] ?? '');
        $passCheck  = validatePasswordRegistration($passRaw);
        $agreePd    = isset($_POST['agree_personal_data']);

        $formValues['first_name'] = $fnCheck['value'];
        $formValues['last_name']  = $lnCheck['value'];
        $formValues['phone']      = $phoneCheck['value'];
        $formValues['email']      = $emailCheck['value'];

        if (!$fnCheck['valid']) {
            $error = $fnCheck['error'];
        } elseif (!$lnCheck['valid']) {
            $error = $lnCheck['error'];
        } elseif (!$phoneCheck['valid']) {
            $error = $phoneCheck['error'];
        } elseif (!$emailCheck['valid']) {
            $error = $emailCheck['error'];
        } elseif (!$passCheck['valid']) {
            $error = $passCheck['error'];
        } elseif ($passRaw !== $confirmRaw) {
            $error = 'Пароли не совпадают.';
        } elseif (!$agreePd) {
            $error = 'Необходимо дать согласие на обработку персональных данных (152-ФЗ).';
        } else {
            $username = generateUniqueUsernameFromEmail($emailCheck['value']);
            $result = registerUser(
                $username,
                $emailCheck['value'],
                $passRaw,
                $fnCheck['value'],
                $lnCheck['value'],
                $phoneCheck['value'],
                'customer',
                true
            );

            if ($result['success']) {
                $user = loginUser($emailCheck['value'], $passRaw);
                if ($user) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role']     = $user['role'];

                    if (!empty($_SESSION['guest_cart'])) {
                        foreach ($_SESSION['guest_cart'] as $p_id => $qty) {
                            addToCart($user['id'], $p_id, $qty);
                        }
                        unset($_SESSION['guest_cart']);
                    }

                    if ($redirect_safe === 'checkout') {
                        header("Location: checkout.php");
                    } else {
                        header("Location: profile.php");
                    }
                    exit;
                }
                header("Location: login.php?registered=1");
                exit;
            } else {
                $error = $result['error'];
            }
        }
    }
}

$page_title = "Регистрация — Территория Тепла";
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-4 py-md-5" style="background-color: var(--bg-page, #f8fafc);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-6">

                <div class="card auth-card border-0 shadow-lg rounded-4 overflow-hidden mb-4 mb-md-5">
                    <div class="card-header text-center py-4 text-white border-0">
                        <span class="badge bg-warning text-dark text-uppercase fw-bold px-3 py-1 mb-2" style="font-size: 0.72rem;">Новый покупатель</span>
                        <h1 class="h3 mb-1 fw-bold text-white">Регистрация</h1>
                        <p class="mb-0 text-white-50 small">Создайте личный кабинет для быстрого оформления заказов</p>
                    </div>

                    <div class="card-body p-4 p-md-5 bg-white">

                        <?php if ($error): ?>
                            <div class="alert alert-danger d-flex align-items-start gap-2 rounded-3 mb-4 small" role="alert">
                                <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                                <div><?= htmlspecialchars($error); ?></div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" novalidate data-auth-form="register">
                            <?= getCsrfField(); ?>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label fw-bold small text-muted text-uppercase">Имя *</label>
                                    <input type="text"
                                           name="first_name"
                                           id="first_name"
                                           class="form-control bg-light"
                                           placeholder="Иван"
                                           autocomplete="given-name"
                                           minlength="2"
                                           maxlength="50"
                                           pattern="[А-Яа-яЁёA-Za-z]+([ \-][А-Яа-яЁёA-Za-z]+)*"
                                           data-rule="human-name"
                                           required
                                           value="<?= htmlspecialchars($formValues['first_name']) ?>">
                                    <span class="form-hint">Только буквы (без цифр и символов)</span>
                                    <div class="field-error-text"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label fw-bold small text-muted text-uppercase">Фамилия *</label>
                                    <input type="text"
                                           name="last_name"
                                           id="last_name"
                                           class="form-control bg-light"
                                           placeholder="Иванов"
                                           autocomplete="family-name"
                                           minlength="2"
                                           maxlength="50"
                                           pattern="[А-Яа-яЁёA-Za-z]+([ \-][А-Яа-яЁёA-Za-z]+)*"
                                           data-rule="human-name"
                                           required
                                           value="<?= htmlspecialchars($formValues['last_name']) ?>">
                                    <span class="form-hint">Только буквы (без цифр и символов)</span>
                                    <div class="field-error-text"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label fw-bold small text-muted text-uppercase">Телефон *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-muted"></i></span>
                                    <input type="tel"
                                           name="phone"
                                           id="phone"
                                           class="form-control border-start-0 bg-light"
                                           placeholder="+7 (999) 123-45-67"
                                           autocomplete="tel"
                                           inputmode="tel"
                                           data-rule="phone"
                                           required
                                           value="<?= htmlspecialchars($formValues['phone']) ?>">
                                </div>
                                <span class="form-hint">Формат РФ: +7 (XXX) XXX-XX-XX</span>
                                <div class="field-error-text"></div>
                            </div>

                            <div class="mb-3">
                                <label for="reg-email" class="form-label fw-bold small text-muted text-uppercase">Email адрес *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email"
                                           name="email"
                                           id="reg-email"
                                           class="form-control border-start-0 bg-light"
                                           placeholder="name@example.ru"
                                           autocomplete="email"
                                           inputmode="email"
                                           maxlength="100"
                                           data-rule="email"
                                           required
                                           value="<?= htmlspecialchars($formValues['email']) ?>">
                                </div>
                                <span class="form-hint">Латинские символы (используется для входа и чеков)</span>
                                <div class="field-error-text"></div>
                            </div>

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label for="reg-password" class="form-label fw-bold small text-muted text-uppercase">Пароль *</label>
                                    <div class="input-group">
                                        <input type="password"
                                               name="password"
                                               id="reg-password"
                                               class="form-control border-end-0 bg-light"
                                               placeholder="Мин. 6 символов"
                                               autocomplete="new-password"
                                               minlength="6"
                                               maxlength="64"
                                               data-rule="password-register"
                                               required>
                                        <button class="btn btn-light border border-start-0 toggle-password-btn" type="button" data-target="reg-password" title="Показать">
                                            <i class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
                                    <div class="field-error-text"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="reg-confirm" class="form-label fw-bold small text-muted text-uppercase">Повтор пароля *</label>
                                    <div class="input-group">
                                        <input type="password"
                                               name="confirm_password"
                                               id="reg-confirm"
                                               class="form-control border-end-0 bg-light"
                                               placeholder="Повторите пароль"
                                               autocomplete="new-password"
                                               minlength="6"
                                               maxlength="64"
                                               data-rule="password-confirm"
                                               required>
                                        <button class="btn btn-light border border-start-0 toggle-password-btn" type="button" data-target="reg-confirm" title="Показать">
                                            <i class="bi bi-eye text-muted"></i>
                                        </button>
                                    </div>
                                    <div class="field-error-text"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="password-strength-track">
                                    <div class="password-strength-fill"></div>
                                </div>
                                <small class="text-muted d-block mt-1 password-strength-label" style="font-size: 0.76rem;">
                                    От 6 символов: латинская буква + цифра, без пробелов и кириллицы
                                </small>
                                <div class="caps-warning"></div>
                            </div>

                            <!-- Согласие на обработку персональных данных -->
                            <div class="mb-4 p-3 bg-light rounded-3 border">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="agree_personal_data" id="agree_personal_data" required
                                        <?= isset($_POST['agree_personal_data']) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="agree_personal_data">
                                        Я даю согласие на
                                        <a href="docs/personal_data_agreement.pdf" target="_blank" class="text-warning fw-bold text-decoration-underline">
                                            обработку персональных данных
                                        </a>
                                        в соответствии с №&nbsp;152-ФЗ <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-pill shadow-sm">
                                    <i class="bi bi-person-plus-fill me-1"></i> Создать аккаунт
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="text-muted mb-0 small">
                                Уже есть аккаунт?
                                <a href="login.php<?= $redirect_safe ? '?redirect=' . urlencode($redirect_safe) : '' ?>" class="text-decoration-none fw-bold text-dark hover-warning ms-1">
                                    Войти
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Блок преимуществ в инженерном стиле -->
                <div class="row text-center g-3">
                    <div class="col-4">
                        <div class="feature-card p-3">
                            <div class="feature-icon-wrapper mx-auto mb-2" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">Быстро</h6>
                            <p class="text-muted mb-0 d-none d-sm-block" style="font-size: 0.78rem;">Заказ в 2 клика</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="feature-card p-3">
                            <div class="feature-icon-wrapper mx-auto mb-2" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                <i class="bi bi-box-seam-fill"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">Удобно</h6>
                            <p class="text-muted mb-0 d-none d-sm-block" style="font-size: 0.78rem;">История заказов</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="feature-card p-3">
                            <div class="feature-icon-wrapper mx-auto mb-2" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <h6 class="fw-bold small mb-1">Выгодно</h6>
                            <p class="text-muted mb-0 d-none d-sm-block" style="font-size: 0.78rem;">Бонусы и сервис</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/imask"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "Контакты и склад — Территория Тепла";
$success_msg = "";
$error_msg = "";

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $consent = isset($_POST['consent_pd']);

    if (empty($name) || empty($phone) || empty($message)) {
        $error_msg = "Пожалуйста, заполните обязательные поля (Имя, Телефон, Сообщение).";
    } elseif (!$consent) {
        $error_msg = "Необходимо согласие на обработку персональных данных (152-ФЗ).";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_requests (name, email, phone, message, status, created_at) VALUES (?, ?, ?, ?, 'new', NOW())");
            if ($stmt->execute([$name, $email, $phone, $message])) {
                $success_msg = "Ваше сообщение успешно отправлено! Специалист склада свяжется с вами в течение 15 минут.";
            } else {
                $error_msg = "Ошибка при отправке сообщения. Пожалуйста, позвоните нам по телефону.";
            }
        } catch (Exception $e) {
            $error_msg = "Ошибка отправки: " . $e->getMessage();
        }
    }
}
include __DIR__ . '/includes/header.php';
?>

<main style="background-color: var(--bg-page, #f8fafc);">
    <!-- Hero заголовок -->
    <div class="py-5 text-white" style="background: linear-gradient(135deg, #181c20 0%, #2b3238 100%); border-bottom: 3px solid var(--bs-warning, #f59e0b);">
        <div class="container text-center py-4">
            <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold mb-3 shadow-sm">
                <i class="bi bi-geo-alt-fill me-1"></i> Прямая связь со складом
            </span>
            <h1 class="display-5 fw-bold text-uppercase mb-2">Контакты и <span class="text-warning">магазин-склад</span></h1>
            <p class="lead text-white-50 mb-0 max-w-600 mx-auto">
                Ответим на любые технические вопросы, рассчитаем диаметр дымохода и поможем подобрать оборудование
            </p>
        </div>
    </div>

    <div class="container py-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Контакты</li>
            </ol>
        </nav>

        <div class="row g-4 g-lg-5 mb-5">
            <!-- Контактная информация -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                    <h4 class="fw-bold mb-4 text-dark border-bottom pb-2">
                        <i class="bi bi-building-fill-check text-warning me-2"></i> Отдел продаж и склад
                    </h4>
                    
                    <!-- Адрес склада -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 44px; height: 44px;">
                            <i class="bi bi-geo-alt-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Основной магазин-склад (Майма)</h6>
                            <div class="text-muted small">Республика Алтай, с. Майма, ул. Энергетиков, д. 24</div>
                            <div class="small text-warning fw-semibold mt-1">
                                <i class="bi bi-clock me-1"></i> Пн–Сб: 09:00 – 18:00 (Вс: выходной)
                            </div>
                        </div>
                    </div>

                    <!-- Телефоны -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 44px; height: 44px;">
                            <i class="bi bi-telephone-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Прямой телефон склада</h6>
                            <a href="tel:+79136914443" class="text-decoration-none text-dark fw-bold d-block fs-5">+7 (913) 691-44-43</a>
                            <span class="text-muted small">Звонки по всей Республике Алтай и краю</span>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 44px; height: 44px;">
                            <i class="bi bi-envelope-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Электронная почта</h6>
                            <a href="mailto:mpterritoriatepla@yandex.ru" class="text-decoration-none text-dark fw-semibold d-block">mpterritoriatepla@yandex.ru</a>
                            <small class="text-muted">Для счетов, договоров и проектов котельных</small>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mt-auto">
                        <small class="text-muted d-block mb-1"><strong>Реквизиты:</strong> ООО «МП ТЕРРИТОРИЯ ТЕПЛА»</small>
                        <small class="text-muted d-block">ИНН 0411174476 | ОГРН 1150411003808</small>
                    </div>
                </div>
            </div>

            <!-- Форма обратной связи -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                    <h4 class="fw-bold mb-4 text-dark border-bottom pb-2">
                        <i class="bi bi-chat-left-dots-fill text-warning me-2"></i> Задать вопрос инженеру склада
                    </h4>
                    
                    <?php if ($success_msg): ?>
                        <div class="alert alert-success d-flex align-items-center rounded-4 mb-4 shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                            <div><?= htmlspecialchars($success_msg); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger d-flex align-items-center rounded-4 mb-4 shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                            <div><?= htmlspecialchars($error_msg); ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Ваше имя *</label>
                                <input type="text" name="name" class="form-control" placeholder="Иван" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Телефон для связи *</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+7 (___) ___-__-__" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Email (необязательно)</label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Что вас интересует? *</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Модель печи, расчет дымохода, выезд мастера, наличие на складе..." required></textarea>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="consent_pd" id="c_pd" required checked>
                            <label class="form-check-label small text-muted" for="c_pd">
                                Даю согласие на обработку персональных данных в соответствии с <a href="terms.php#privacy" target="_blank" class="text-dark fw-semibold">152-ФЗ</a> *
                            </label>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-pill shadow-sm fs-5">
                            Отправить вопрос инженеру <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Карта склада -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header py-3" style="background: var(--dark-main, #181c20); color: #fff;">
                <h5 class="mb-0 fw-bold fs-6">
                    <i class="bi bi-geo-alt text-warning me-2"></i> Схема проезда к магазину-складу (с. Майма, ул. Энергетиков, 24)
                </h5>
            </div>
            <div class="card-body p-0">
                <iframe src="https://yandex.ru/map-widget/v1/?ll=85.905756%2C52.004100&z=16&pt=85.905756%2C52.004100" width="100%" height="450" frameborder="0" loading="lazy" style="border:0; display:block;"></iframe>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

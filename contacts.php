<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "Контакты — Михаил Потапыч";
$success_msg = "";
$error_msg = "";

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        $error_msg = "Пожалуйста, заполните все обязательные поля.";
    } else {
        // Сохранение в БД
        $stmt = $pdo->prepare("INSERT INTO contact_requests (name, email, phone, message) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $phone, $message])) {
            $success_msg = "Ваше сообщение успешно отправлено! Мы свяжемся с вами в ближайшее время.";
        } else {
            $error_msg = "Ошибка при отправке сообщения. Попробуйте позже.";
        }
    }
}
include __DIR__ . '/includes/header.php';
?>

<main class="bg-light">
    <!-- Hero заголовок -->
    <div class="hero-gradient text-white py-5">
        <div class="container text-center py-3">
            <h1 class="display-5 fw-bold text-uppercase mb-2">Контакты и <span class="text-warning">связь с нами</span></h1>
            <p class="lead text-white-50 mb-0">Ответим на любые вопросы по отоплению и поможем с выбором оборудования</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4 g-lg-5">
            
            <!-- Контактная информация -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h4 class="fw-bold mb-4 text-dark border-bottom pb-2">Наши контакты</h4>
                    
                    <div class="d-flex align-items-start mb-4">
                        <div class="feature-icon-box flex-shrink-0 me-3">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Адрес шоурума и склада</h6>
                            <p class="text-muted small mb-0">улица Ленина, 1/2, село Майма, Майминский район, Республика Алтай, 649100</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <div class="feature-icon-box flex-shrink-0 me-3">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Телефон</h6>
                            <p class="text-muted small mb-0">
                                <a href="tel:+74951234567" class="text-decoration-none text-dark fw-bold">+7 (495) 123-45-67</a><br>
                                <span class="text-muted">Пн-Пт: 9:00 - 18:00, Сб: 10:00 - 16:00</span>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <div class="feature-icon-box flex-shrink-0 me-3">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Электронная почта</h6>
                            <p class="text-muted small mb-0">
                                <a href="mailto:info@pechi-kotly.ru" class="text-decoration-none text-dark">info@pechi-kotly.ru</a>
                            </p>
                        </div>
                    </div>

                    <hr class="my-3">

                    <h6 class="fw-bold mb-3 text-dark">Мы в мессенджерах и соцсетях</h6>
                    <div class="d-flex gap-2">
                        <a href="https://t.me" target="_blank" class="btn btn-dark rounded-circle shadow-sm" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-telegram fs-5"></i></a>
                        <a href="https://wa.me" target="_blank" class="btn btn-success rounded-circle shadow-sm" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-whatsapp fs-5"></i></a>
                        <a href="https://youtube.com" target="_blank" class="btn btn-danger rounded-circle shadow-sm" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-youtube fs-5"></i></a>
                    </div>
                </div>
            </div>

            <!-- Форма обратной связи -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                    <h4 class="fw-bold mb-4 text-dark border-bottom pb-2">Напишите нам</h4>
                    
                    <?php if ($success_msg): ?>
                        <div class="alert alert-success d-flex align-items-center rounded-3 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                            <div><?= htmlspecialchars($success_msg) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger d-flex align-items-center rounded-3 mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                            <div><?= htmlspecialchars($error_msg) ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="contacts.php">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold small text-uppercase text-muted">Ваше имя *</label>
                                <input type="text" class="form-control bg-light" id="name" name="name" placeholder="Иван Иванов" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-bold small text-uppercase text-muted">Телефон</label>
                                <input type="tel" class="form-control bg-light" id="phone" name="phone" placeholder="+7 (999) 000-00-00">
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label fw-bold small text-uppercase text-muted">Email *</label>
                                <input type="email" class="form-control bg-light" id="email" name="email" placeholder="name@example.com" required>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label fw-bold small text-uppercase text-muted">Сообщение *</label>
                                <textarea class="form-control bg-light" id="message" name="message" rows="4" placeholder="Ваш вопрос или пожелание..." required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold rounded-pill shadow-sm">
                                    <i class="bi bi-send-fill me-1"></i> Отправить сообщение
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Карта -->
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-dark text-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-map-fill text-warning me-2"></i>Схема проезда</h6>
                    </div>
                    <iframe src="https://yandex.ru/map-widget/v1/?ll=85.905756%2C52.004100&z=17&pt=85.905756%2C52.004100" width="100%" height="400" frameborder="0" loading="lazy"></iframe>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

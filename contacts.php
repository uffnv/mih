<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "Контакты";
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
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="py-5 bg-light">
    <div class="container">
        
        <div class="text-center mb-5">
            <h1 class="fw-bold text-uppercase">Свяжитесь с <span class="text-warning">Нами</span></h1>
            <p class="lead text-muted">Мы всегда рады ответить на ваши вопросы и помочь с выбором</p>
        </div>

        <div class="row g-5">
            
            <!-- Контактная информация -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">Наши контакты</h3>
                        
                        <div class="d-flex align-items-start mb-4">
                            <div class="bg-warning text-dark rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-geo-alt-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Адрес</h5>
                                <p class="text-muted mb-0">улица Ленина, 1/2, село Майма, Майминский район, Республика Алтай, 649100</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-4">
                            <div class="bg-warning text-dark rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-telephone-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Телефон</h5>
                                <p class="text-muted mb-0">
                                    <a href="tel:+74951234567" class="text-decoration-none text-dark">+7 (495) 123-45-67</a><br>
                                    <span class="small text-muted">Пн-Пт: 9:00 - 18:00</span>
                                </p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-4">
                            <div class="bg-warning text-dark rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-envelope-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Email</h5>
                                <p class="text-muted mb-0">
                                    <a href="mailto:info@pechi-kotly.ru" class="text-decoration-none text-dark">info@pechi-kotly.ru</a>
                                </p>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3">Мы в соцсетях</h5>
                        <div class="d-flex gap-3">
                            <a href="#" class="btn btn-outline-dark rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-telegram"></i></a>
                            <a href="#" class="btn btn-outline-dark rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-whatsapp"></i></a>
                            <a href="#" class="btn btn-outline-dark rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Форма обратной связи -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="fw-bold mb-4">Напишите нам</h3>
                        
                        <?php if ($success_msg): ?>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <div><?= htmlspecialchars($success_msg) ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($error_msg): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div><?= htmlspecialchars($error_msg) ?></div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="contacts.php">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-bold small text-uppercase text-muted">Ваше имя *</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Иван Иванов" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold small text-uppercase text-muted">Телефон</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+7 (999) 000-00-00">
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label fw-bold small text-uppercase text-muted">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label fw-bold small text-uppercase text-muted">Сообщение *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Ваш вопрос или комментарий..." required></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold">Отправить сообщение</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Карта -->
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <iframe src="https://yandex.ru/map-widget/v1/?ll=85.905756%2C52.004100&z=17&pt=85.905756%2C52.004100" width="100%" height="450" frameborder="0"></iframe>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

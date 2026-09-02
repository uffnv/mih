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
            <h1 class="display-5 fw-bold text-uppercase mb-2">Контакты и <span class="text-warning">магазины</span></h1>
            <p class="lead text-white-50 mb-0">Ответим на любые вопросы по отоплению и поможем с выбором оборудования</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row g-4 g-lg-5">
            
            <!-- Контактная информация -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h4 class="fw-bold mb-4 text-dark border-bottom pb-2">
                        <i class="bi bi-geo-alt-fill text-warning me-2"></i>Наши контакты
                    </h4>
                    
                    <!-- Адреса магазинов -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="feature-icon-box flex-shrink-0 me-3">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-2 text-dark">Адреса магазинов и складов</h6>
                            <div class="mb-2">
                                <span class="badge bg-warning text-dark me-1">Магазин 1 (Склад)</span>
                                <div class="text-dark small fw-medium mt-1">с. Майма, ул. Ленина, д. 1/1 (1/2), Офис 4</div>
                            </div>
                            <div>
                                <span class="badge bg-dark text-white me-1">Магазин 2</span>
                                <div class="text-dark small fw-medium mt-1">г. Горно-Алтайск, ул. Чорос-Гуркина, 9А (ТЦ «Кристалл»)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Телефоны -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="feature-icon-box flex-shrink-0 me-3">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Телефоны отдела продаж</h6>
                            <p class="text-muted small mb-0">
                                <a href="tel:+79136914443" class="text-decoration-none text-dark fw-bold d-block fs-6">+7 (913) 691-44-43</a>
                                <a href="tel:89609681000" class="text-decoration-none text-muted d-block small">8-960-968-1000</a>
                                <span class="text-muted small mt-1 d-block"><i class="bi bi-clock me-1"></i>Пн-Пт 9:00 - 18:00, Сб 10:00 - 16:00</span>
                            </p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="feature-icon-box flex-shrink-0 me-3">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Электронная почта</h6>
                            <p class="text-muted small mb-0">
                                <a href="mailto:mpterritoriatepla@yandex.ru" class="text-decoration-none text-dark fw-bold d-block">mpterritoriatepla@yandex.ru</a>
                                <a href="mailto:admin561245@territoriatepla.ru" class="text-decoration-none text-dark d-block small">admin561245@territoriatepla.ru</a>
                                <a href="mailto:amips@mail.ru" class="text-decoration-none text-muted d-block small">amips@mail.ru</a>
                            </p>
                        </div>
                    </div>

                    <hr class="my-3">

                    <h6 class="fw-bold mb-3 text-dark">Связь через мессенджеры</h6>
                    <div class="d-flex align-items-center gap-2">
                        <a href="https://t.me" target="_blank" class="btn btn-dark rounded-circle shadow-sm" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;" title="Telegram">
                            <i class="bi bi-telegram fs-5"></i>
                        </a>
                        <!-- Иконка MAX -->
                        <a href="#" class="shadow-sm rounded-3 d-inline-flex align-items-center justify-content-center text-decoration-none" style="width: 44px; height: 44px; background: #ffc107;" title="Мессенджер MAX">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                                <rect width="32" height="32" rx="8" fill="#ffc107"/>
                                <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" font-size="11" font-weight="900" font-family="system-ui, -apple-system, sans-serif" fill="#111827" letter-spacing="0.5">MAX</text>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Форма обратной связи -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                    <h4 class="fw-bold mb-4 text-dark border-bottom pb-2">
                        <i class="bi bi-chat-left-dots-fill text-warning me-2"></i>Напишите нам
                    </h4>
                    
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

                            <div class="col-12">
                                <div class="form-check small text-muted">
                                    <input class="form-check-input" type="checkbox" id="contact_agree" checked required>
                                    <label class="form-check-label" for="contact_agree">
                                        Нажимая кнопку, вы соглашаетесь на 
                                        <a href="docs/personal_data_agreement.pdf" target="_blank" class="text-dark fw-bold text-decoration-underline">
                                            обработку персональных данных
                                        </a> в соответствии с 152-ФЗ
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold rounded-pill shadow-sm">
                                    <i class="bi bi-send-fill me-1"></i> Отправить сообщение
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Блок юридических реквизитов (Требование законодательства РФ) -->
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold fs-6">
                            <i class="bi bi-building-fill-check text-warning me-2"></i>Реквизиты организации (ООО «МП ТЕРРИТОРИЯ ТЕПЛА»)
                        </h5>
                        <span class="badge bg-warning text-dark">Официальные данные</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted text-nowrap" style="width: 170px;">Полное наименование:</td>
                                            <td class="fw-bold text-dark">ООО «МП ТЕРРИТОРИЯ ТЕПЛА»</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Генеральный директор:</td>
                                            <td class="fw-bold text-dark">Папин Михаил Николаевич</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">ИНН:</td>
                                            <td class="fw-bold text-dark">0411174476</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">КПП:</td>
                                            <td class="fw-bold text-dark">041101001</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">ОГРН:</td>
                                            <td class="fw-bold text-dark">1150411003808</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Юридический адрес:</td>
                                            <td class="text-dark">649100 Республика Алтай, м.р-н Майминский, с.п. Майминское, с. Майма, ул. Ленина, д. 1/1, Офис 4</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Фактический адрес:</td>
                                            <td class="text-dark">649100 Республика Алтай, м.р-н Майминский, с.п. Майминское, с. Майма, ул. Ленина, д. 1/1, Офис 4</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 border-start-md">
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted text-nowrap" style="width: 170px;">Банк:</td>
                                            <td class="fw-bold text-dark">ОМСКОЕ ОТДЕЛЕНИЕ № 8634 ПАО СБЕРБАНК</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Расчетный счет:</td>
                                            <td class="fw-bold text-dark font-monospace">40702810602350000763</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Корреспондентский счет:</td>
                                            <td class="fw-bold text-dark font-monospace">30101810900000000673</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">БИК:</td>
                                            <td class="fw-bold text-dark font-monospace">045209673</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Телефон:</td>
                                            <td class="text-dark"><a href="tel:+79136914443" class="text-decoration-none text-dark fw-bold">+7 (913) 691-44-43</a>, 8-960-968-1000</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Email:</td>
                                            <td class="text-dark"><a href="mailto:mpterritoriatepla@yandex.ru" class="text-decoration-none text-dark">mpterritoriatepla@yandex.ru</a>, admin561245@territoriatepla.ru</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Карты магазинов: 2 точки -->
            <div class="col-12">
                <div class="row g-4">
                    <!-- Карта 1: с. Майма -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                            <div class="card-header bg-dark text-white py-3">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-geo-alt-fill text-warning me-2"></i>Магазин и склад в с. Майма
                                </h6>
                                <small class="text-white-50">ул. Ленина, 1/1 (1/2), Офис 4</small>
                            </div>
                            <div class="card-body p-0">
                                <iframe src="https://yandex.ru/map-widget/v1/?ll=85.905756%2C52.004100&z=17&pt=85.905756%2C52.004100" width="100%" height="420" frameborder="0" loading="lazy" style="border:0; display:block;"></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Карта 2: г. Горно-Алтайск (ТЦ "Кристалл") -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                            <div class="card-header bg-dark text-white py-3">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-geo-alt-fill text-warning me-2"></i>Магазин в г. Горно-Алтайск
                                </h6>
                                <small class="text-white-50">ул. Чорос-Гуркина, 9А (ТЦ «Кристалл»)</small>
                            </div>
                            <div class="card-body p-0">
                                <iframe src="https://yandex.ru/map-widget/v1/?ll=85.967192%2C51.956689&z=17&pt=85.967192%2C51.956689" width="100%" height="420" frameborder="0" loading="lazy" style="border:0; display:block;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

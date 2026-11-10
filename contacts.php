<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "Контакты и магазины — Территория Тепла";
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
                $success_msg = "Ваше сообщение успешно отправлено! Специалист свяжется с вами в течение 15 минут.";
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
                <i class="bi bi-geo-alt-fill me-1"></i> Точки продаж и склад в Республике Алтай
            </span>
            <h1 class="display-5 fw-bold text-uppercase mb-2 text-white">Контакты и <span class="text-warning">магазины</span></h1>
            <p class="lead text-white-50 mb-0 max-w-600 mx-auto">
                Ответим на любые технические вопросы, рассчитаем систему отопления и поможем подобрать оборудование
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
                        <i class="bi bi-building-fill-check text-warning me-2"></i> Отдел продаж и склады
                    </h4>
                    
                    <!-- Адреса филиалов -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 44px; height: 44px;">
                            <i class="bi bi-shop fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-2 text-dark">Наши филиалы</h6>
                            
                            <div class="mb-3 p-2 rounded-3 bg-light border">
                                <span class="badge bg-warning text-dark fw-bold mb-1">Точка 1: Склад-магазин (Майма)</span>
                                <div class="text-dark small fw-medium">с. Майма, ул. Ленина, д. 1/1 (1/2), Офис 4 / ул. Энергетиков, 24</div>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-clock me-1 text-warning"></i> Пн–Сб: 09:00 – 18:00 (Вс: выходной)
                                </div>
                            </div>

                            <div class="p-2 rounded-3 bg-light border">
                                <span class="badge bg-dark text-white fw-bold mb-1">Точка 2: Магазин (Горно-Алтайск)</span>
                                <div class="text-dark small fw-medium">г. Горно-Алтайск, ул. Чорос-Гуркина, 9А (ТЦ «Кристалл»)</div>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-clock me-1 text-warning"></i> Пн–Вс: 10:00 – 19:00 (без выходных)
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Телефоны -->
                    <div class="d-flex align-items-start mb-4">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0 me-3" style="width: 44px; height: 44px;">
                            <i class="bi bi-telephone-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Телефоны отдела продаж</h6>
                            <a href="tel:+79136914443" class="text-decoration-none text-dark fw-bold d-block fs-5">+7 (913) 691-44-43</a>
                            <a href="tel:89609681000" class="text-decoration-none text-muted d-block small">8-960-968-1000</a>
                            <small class="text-muted">Звонки по всей Республике Алтай и краю</small>
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
                            <a href="mailto:admin561245@territoriatepla.ru" class="text-decoration-none text-muted d-block small">admin561245@territoriatepla.ru</a>
                            <small class="text-muted">Для счетов, договоров и проектов котельных</small>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mt-auto border">
                        <small class="text-muted d-block mb-1"><strong>Организация:</strong> ООО «МП ТЕРРИТОРИЯ ТЕПЛА»</small>
                        <small class="text-muted d-block">ИНН 0411174476 | ОГРН 1150411003808</small>
                    </div>
                </div>
            </div>

            <!-- Форма обратной связи -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                    <h4 class="fw-bold mb-4 text-dark border-bottom pb-2">
                        <i class="bi bi-chat-left-dots-fill text-warning me-2"></i> Задать вопрос инженеру
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
                                Даю согласие на обработку персональных данных в соответствии с <a href="terms.php#privacy" target="_blank" class="text-dark fw-semibold text-decoration-underline">152-ФЗ</a> *
                            </label>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-pill shadow-sm fs-5">
                            <i class="bi bi-send-fill me-1"></i> Отправить вопрос инженеру
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Юридические реквизиты (полная таблица как в оригинале) -->
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-5">
            <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center" style="background: var(--dark-main, #181c20); color: #fff;">
                <h5 class="mb-0 fw-bold fs-6 text-white">
                    <i class="bi bi-shield-check text-warning me-2"></i> Реквизиты организации (ООО «МП ТЕРРИТОРИЯ ТЕПЛА»)
                </h5>
                <span class="badge bg-warning text-dark fw-bold">Официальные данные</span>
            </div>
            <div class="card-body p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0 small">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 170px;">Полное наименование:</td>
                                    <td class="fw-bold text-dark">ООО «МП ТЕРРИТОРИЯ ТЕПЛА»</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Генеральный директор:</td>
                                    <td class="fw-bold text-dark">Папин Михаил Николаевич</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ИНН:</td>
                                    <td class="fw-bold text-dark font-monospace">0411174476</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">КПП:</td>
                                    <td class="fw-bold text-dark font-monospace">041101001</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ОГРН:</td>
                                    <td class="fw-bold text-dark font-monospace">1150411003808</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Юридический адрес:</td>
                                    <td class="text-dark">649100 Республика Алтай, м.р-н Майминский, с.п. Майминское, с. Майма, ул. Ленина, д. 1/1, Офис 4</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Фактический склад:</td>
                                    <td class="text-dark">649100 Республика Алтай, с. Майма, ул. Энергетиков, д. 24</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 border-start-md">
                        <table class="table table-sm table-borderless mb-0 small">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 170px;">Банк:</td>
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
                                    <td class="text-muted">Телефоны:</td>
                                    <td class="text-dark"><a href="tel:+79136914443" class="text-decoration-none text-dark fw-bold">+7 (913) 691-44-43</a>, 8-960-968-1000</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email:</td>
                                    <td class="text-dark"><a href="mailto:mpterritoriatepla@yandex.ru" class="text-decoration-none text-dark">mpterritoriatepla@yandex.ru</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Карты магазинов: 2 точки (как в оригинале) -->
        <div class="row g-4">
            <!-- Карта 1: с. Майма -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                    <div class="card-header py-3 px-4" style="background: var(--dark-main, #181c20); color: #fff;">
                        <h6 class="mb-0 fw-bold text-white">
                            <i class="bi bi-geo-alt-fill text-warning me-2"></i> Магазин и склад в с. Майма
                        </h6>
                        <small class="text-white-50">ул. Ленина, 1/1 (1/2), Офис 4 / ул. Энергетиков, 24</small>
                    </div>
                    <div class="card-body p-0">
                        <iframe src="https://yandex.ru/map-widget/v1/?ll=85.905756%2C52.004100&z=17&pt=85.905756%2C52.004100" width="100%" height="420" frameborder="0" loading="lazy" style="border:0; display:block;"></iframe>
                    </div>
                </div>
            </div>

            <!-- Карта 2: г. Горно-Алтайск (ТЦ "Кристалл") -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                    <div class="card-header py-3 px-4" style="background: var(--dark-main, #181c20); color: #fff;">
                        <h6 class="mb-0 fw-bold text-white">
                            <i class="bi bi-geo-alt-fill text-warning me-2"></i> Магазин в г. Горно-Алтайск (ТЦ «Кристалл»)
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
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

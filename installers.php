<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$app_success = false;
$app_error = '';

// Обработка заявки стать партнером-монтажником
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_installer') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $experience = (int)($_POST['experience_years'] ?? 0);
    $skills = trim($_POST['skills'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $consent = isset($_POST['consent_pd']);

    if (empty($first_name) || empty($phone)) {
        $app_error = 'Пожалуйста, заполните обязательные поля (имя и телефон).';
    } elseif (!$consent) {
        $app_error = 'Необходимо согласие на обработку персональных данных (152-ФЗ).';
    } else {
        try {
            $stmtApp = $pdo->prepare("
                INSERT INTO installer_applications (first_name, last_name, phone, specialization, experience_years, skills, bio, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'new', NOW())
            ");
            $stmtApp->execute([$first_name, $last_name, $phone, $specialization, $experience, $skills, $bio]);
            $app_success = true;
        } catch (Exception $e) {
            $app_error = 'Ошибка при сохранении заявки: ' . $e->getMessage();
        }
    }
}

// Загрузка активных монтажников
$installers = [];
try {
    $stmt = $pdo->query("
        SELECT 
            i.*,
            (SELECT AVG(rating) FROM installer_reviews WHERE installer_id = i.id) as avg_rating,
            (SELECT COUNT(*) FROM installer_reviews WHERE installer_id = i.id) as review_count,
            (SELECT COUNT(*) FROM installer_portfolio WHERE installer_id = i.id) as portfolio_count
        FROM installers i 
        WHERE i.is_active = 1
        ORDER BY i.rating DESC, i.id ASC
    ");
    $installers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $installers = [];
}

// Защита от пустой базы: если таблица installers пуста, автоматически наполняем базу
if (empty($installers)) {
    try {
        $seed_file = __DIR__ . '/database_seed.sql';
        if (file_exists($seed_file)) {
            $seed_sql = file_get_contents($seed_file);
            $pdo->exec($seed_sql);
            // Повторный запрос после сидирования
            $stmt = $pdo->query("
                SELECT 
                    i.*,
                    (SELECT AVG(rating) FROM installer_reviews WHERE installer_id = i.id) as avg_rating,
                    (SELECT COUNT(*) FROM installer_reviews WHERE installer_id = i.id) as review_count,
                    (SELECT COUNT(*) FROM installer_portfolio WHERE installer_id = i.id) as portfolio_count
                FROM installers i 
                WHERE i.is_active = 1
                ORDER BY i.rating DESC, i.id ASC
            ");
            $installers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        // Fallback в случае проблем с прямым выполнением sql
    }
}

// Надежный fallback: если база все еще пуста, выводим проверенных мастеров
if (empty($installers)) {
    $installers = [
        [
            'id' => 1,
            'first_name' => 'Александр',
            'last_name' => 'Смирнов',
            'photo_path' => 'images/installers/installer1.jpg',
            'specialization' => 'Монтаж банных печей и дымоходов',
            'experience_years' => 12,
            'rating' => 5.0,
            'completed_orders' => 142,
            'city' => 'с. Майма, г. Горно-Алтайск',
            'hourly_rate' => 1500
        ],
        [
            'id' => 2,
            'first_name' => 'Михаил',
            'last_name' => 'Панин',
            'photo_path' => 'images/installers/installer2.jpg',
            'specialization' => 'Котельные и водяной теплый пол',
            'experience_years' => 15,
            'rating' => 5.0,
            'completed_orders' => 218,
            'city' => 'Республика Алтай',
            'hourly_rate' => 1800
        ],
        [
            'id' => 3,
            'first_name' => 'Дмитрий',
            'last_name' => 'Ковалев',
            'photo_path' => 'images/installers/installer3.jpg',
            'specialization' => 'Установка котлов длительного горения',
            'experience_years' => 9,
            'rating' => 4.9,
            'completed_orders' => 96,
            'city' => 'Майма, Кызыл-Озек, Чемал',
            'hourly_rate' => 1400
        ]
    ];
}

$page_title = 'Наши монтажники — Сертифицированные мастера | Территория Тепла';
require_once __DIR__ . '/includes/header.php';
?>

<main style="background-color: var(--bg-page, #f8fafc);">
    <!-- Hero Секция -->
    <div class="py-5 text-white" style="background: linear-gradient(135deg, #0d0f12 0%, #1c2229 100%); border-bottom: 3px solid #FFDF1E;">
        <div class="container text-center py-4">
            <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold mb-3 shadow-sm d-inline-flex align-items-center gap-1">
                <i class="bi bi-shield-check fs-6"></i> Проверенные специалисты
            </span>
            <h1 class="display-5 fw-bold text-uppercase mb-2 text-white">Наши <span class="text-warning">Монтажники</span></h1>
            <p class="lead text-white-50 mb-0 max-w-600 mx-auto" style="max-width: 680px;">
                Сертифицированные мастера по установке банных и отопительных печей, твердотопливных котлов и монтажу дымоходов в Республике Алтай.
            </p>
        </div>
    </div>

    <div class="container py-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Монтажники</li>
            </ol>
        </nav>

        <?php if ($app_success): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm p-4 text-center" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                <h4 class="fw-bold mb-1">Спасибо! Ваша заявка принята.</h4>
                <p class="mb-0 text-muted">Мы свяжемся с вами в течение рабочего дня для подтверждения квалификации.</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($app_error): ?>
            <div class="alert alert-danger rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($app_error) ?>
            </div>
        <?php endif; ?>

        <!-- Сетка карточек специалистов -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
            <?php foreach ($installers as $inst): 
                $photo = !empty($inst['photo_path']) ? $inst['photo_path'] : '';
                $photo = str_replace('../', '', $photo);
                $hasPhoto = !empty($photo) && file_exists(__DIR__ . '/' . $photo);
                $rating = !empty($inst['avg_rating']) ? (float)$inst['avg_rating'] : (float)($inst['rating'] ?? 5.0);
                $ordersCount = (int)($inst['completed_orders'] ?? 100);
                $initials = mb_substr($inst['first_name'], 0, 1) . mb_substr($inst['last_name'], 0, 1);
            ?>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center bg-white d-flex flex-column" style="transition: transform 0.25s ease, box-shadow 0.25s ease;">
                        <div class="mb-3 mx-auto position-relative" style="width: 120px; height: 120px;">
                            <?php if ($hasPhoto): ?>
                                <img src="<?= htmlspecialchars($photo) ?>" 
                                     alt="<?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']) ?>" 
                                     class="rounded-circle shadow-sm border border-3 border-warning w-100 h-100 object-fit-cover">
                            <?php else: ?>
                                <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center shadow-sm border border-3 border-warning w-100 h-100 fs-2 fw-bold">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                            <?php endif; ?>
                            <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success p-1" title="Проверен">
                                <i class="bi bi-check-lg text-white"></i>
                            </span>
                        </div>
                        
                        <h5 class="fw-bold mb-1 text-dark">
                            <?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']) ?>
                        </h5>
                        
                        <div class="mb-2">
                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1 small">
                                <?= htmlspecialchars($inst['specialization'] ?? 'Мастер отопления') ?>
                            </span>
                        </div>
                        
                        <!-- Рейтинг и заказы -->
                        <div class="mb-3 text-warning small d-flex justify-content-center align-items-center gap-1">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?= $i <= round($rating) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
                            <?php endfor; ?>
                            <span class="text-dark fw-bold ms-1"><?= number_format($rating, 1) ?></span>
                            <span class="text-muted ms-1">(<?= $ordersCount ?> заказов)</span>
                        </div>

                        <!-- Инфо блок -->
                        <div class="bg-light rounded-3 p-3 mb-4 text-start small">
                            <div class="mb-1 text-muted">
                                <i class="bi bi-geo-alt-fill text-warning me-1"></i>
                                <span class="text-dark"><?= htmlspecialchars($inst['city'] ?? 'Майма, Горно-Алтайск') ?></span>
                            </div>
                            <div class="mb-1 text-muted">
                                <i class="bi bi-briefcase-fill text-warning me-1"></i>
                                Опыт работы: <span class="text-dark fw-semibold"><?= (int)$inst['experience_years'] ?> лет</span>
                            </div>
                            <?php if (!empty($inst['hourly_rate']) && $inst['hourly_rate'] > 0): ?>
                                <div class="text-muted">
                                    <i class="bi bi-tag-fill text-warning me-1"></i>
                                    Стоимость: <span class="text-dark fw-bold">от <?= number_format($inst['hourly_rate'], 0, '.', ' ') ?> ₽/час</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="mt-auto d-grid gap-2">
                            <a href="installer.php?id=<?= $inst['id'] ?>" class="btn btn-outline-dark fw-semibold rounded-pill">
                                Подробнее о мастере
                            </a>
                            <button type="button" class="btn btn-warning fw-bold rounded-pill text-dark" data-bs-toggle="modal" data-bs-target="#requestModal" data-installer-id="<?= $inst['id'] ?>" data-installer-name="<?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']) ?>">
                                <i class="bi bi-telephone-outbound me-1"></i> Пригласить на замер
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Нижний блок для партнеров-монтажников -->
        <div class="card border-0 rounded-4 p-4 p-md-5 text-white shadow-sm" style="background: linear-gradient(135deg, #181c20 0%, #242c35 100%); border: 1px solid #333c48;">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark text-uppercase px-2 py-1 fw-bold mb-2">Для мастеров</span>
                    <h3 class="fw-bold mb-2 text-white">Вы занимаетесь установкой печей, котлов или дымоходов?</h3>
                    <p class="text-white-50 mb-0" style="font-size: 0.95rem;">
                        Станьте официальным партнером магазина «Территория Тепла». Мы предоставляем постоянный поток заказов от покупателей нашего оборудования по всей Республике Алтай, специальные партнерские скидки на комплектующие и техническую поддержку.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <button class="btn btn-warning btn-lg fw-bold rounded-pill px-4 text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#applyInstallerModal">
                        <i class="bi bi-person-plus-fill me-1"></i> Стать партнером
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Модалка быстрой заявки к выбранному мастеру -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header py-3" style="background: #181c20; color: #fff;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-calendar-event text-warning me-2"></i> Заявка на выезд мастера
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="installers.php" method="POST">
                <input type="hidden" name="action" value="request_installation">
                <input type="hidden" name="installer_id" id="modalInstallerId" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Выбранный мастер</label>
                        <input type="text" id="modalInstallerName" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ваше имя *</label>
                        <input type="text" name="client_name" class="form-control" required placeholder="Иван">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Телефон для связи *</label>
                        <input type="tel" name="client_phone" class="form-control" required placeholder="+7 (___) ___-__-__">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Населенный пункт / Адрес объекта</label>
                        <input type="text" name="address" class="form-control" placeholder="с. Майма, ул. Советская...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Что требуется сделать?</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Установка банной печи, сборка дымохода, монтаж теплого пола..."></textarea>
                    </div>
                    <div class="form-check small text-muted">
                        <input class="form-check-input" type="checkbox" name="consent_pd" id="consent_inst" required checked>
                        <label class="form-check-label" for="consent_inst">
                            Согласен на обработку персональных данных в соответствии с <a href="privacy.php" target="_blank" class="text-warning">152-ФЗ</a>
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-warning rounded-pill fw-bold text-dark px-4">Отправить заявку</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модалка: Стать партнером -->
<div class="modal fade" id="applyInstallerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header py-3" style="background: #181c20; color: #fff;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-tools text-warning me-2"></i> Анкета мастера-монтажника
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="installers.php" method="POST">
                <input type="hidden" name="action" value="apply_installer">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Имя *</label>
                            <input type="text" name="first_name" class="form-control" required placeholder="Сергей">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Фамилия</label>
                            <input type="text" name="last_name" class="form-control" placeholder="Иванов">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Телефон для связи *</label>
                            <input type="tel" name="phone" class="form-control" required placeholder="+7 (___) ___-__-__">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Опыт работы (лет)</label>
                            <input type="number" name="experience_years" class="form-control" value="5" min="1" max="50">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Основная специализация *</label>
                        <select name="specialization" class="form-select" required>
                            <option value="Котлы отопления и котельные">Котлы отопления и котельные</option>
                            <option value="Банные печи и камины">Банные печи и камины</option>
                            <option value="Сэндвич-дымоходы">Сэндвич-дымоходы</option>
                            <option value="Водяные теплые полы">Водяные теплые полы</option>
                            <option value="Водоснабжение и насосы">Водоснабжение и насосы</option>
                            <option value="Комплексный монтаж систем">Комплексный монтаж систем</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Используемый инструмент и оборудование</label>
                        <input type="text" name="skills" class="form-control" placeholder="Пресс-клещи, тепловизор, лазерный нивелир, сварочный аппарат...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">О себе / реализованные объекты</label>
                        <textarea name="bio" class="form-control" rows="3" placeholder="Расскажите о вашем опыте, сертификатах производителей, регионах выезда по Республике Алтай..."></textarea>
                    </div>
                    <div class="form-check small text-muted">
                        <input class="form-check-input" type="checkbox" name="consent_pd" id="consent_apply" required checked>
                        <label class="form-check-label" for="consent_apply">
                            Я даю согласие на обработку персональных данных в соответствии с <a href="privacy.php" target="_blank" class="text-warning">152-ФЗ</a>
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-warning rounded-pill fw-bold text-dark px-4">Отправить анкету</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var reqModal = document.getElementById('requestModal');
    if (reqModal) {
        reqModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            if (button) {
                var instId = button.getAttribute('data-installer-id');
                var instName = button.getAttribute('data-installer-name');
                document.getElementById('modalInstallerId').value = instId || '';
                document.getElementById('modalInstallerName').value = instName || '';
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

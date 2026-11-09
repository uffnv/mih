<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$app_success = false;
$app_error = '';

// Обработка заявки мастера на партнерство
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
        $app_error = 'Пожалуйста, заполните обязательные поля (Имя и Телефон).';
    } elseif (!$consent) {
        $app_error = 'Необходимо согласие на обработку персональных данных.';
    } else {
        try {
            $stmtApp = $pdo->prepare("
                INSERT INTO installer_applications (first_name, last_name, phone, specialization, experience_years, skills, bio, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'new', NOW())
            ");
            $stmtApp->execute([$first_name, $last_name, $phone, $specialization, $experience, $skills, $bio]);
            $app_success = true;
        } catch (Exception $e) {
            $app_error = 'Ошибка при отправке анкеты: ' . $e->getMessage();
        }
    }
}

$page_title = 'Монтажники и сервисные мастера — Территория Тепла';
require_once __DIR__ . '/includes/header.php';

// Получаем список активных мастеров
$stmt = $pdo->query("
    SELECT 
        i.*,
        (SELECT AVG(rating) FROM installer_reviews WHERE installer_id = i.id) as avg_rating,
        (SELECT COUNT(*) FROM installer_reviews WHERE installer_id = i.id) as review_count,
        (SELECT COUNT(*) FROM installer_portfolio WHERE installer_id = i.id) as portfolio_count
    FROM installers i 
    WHERE i.is_active = 1
    ORDER BY avg_rating DESC, id ASC
");
$installers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main style="background-color: var(--bg-page, #f8fafc);">
    <!-- Hero заголовок -->
    <div class="py-5 text-white" style="background: linear-gradient(135deg, #181c20 0%, #2b3238 100%); border-bottom: 3px solid var(--bs-warning, #f59e0b);">
        <div class="container text-center py-4">
            <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold mb-3 shadow-sm">
                <i class="bi bi-shield-check me-1"></i> Проверенные специалисты
            </span>
            <h1 class="display-5 fw-bold text-uppercase mb-2">Наши <span class="text-warning">Монтажники</span></h1>
            <p class="lead text-white-50 mb-0 max-w-600 mx-auto">
                Сертифицированные мастера по установке банных и отопительных печей, твердотопливных котлов и монтажу дымоходов в Республике Алтай
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
                <h4 class="fw-bold mb-1">Спасибо! Ваша анкета принята.</h4>
                <p class="mb-0 text-muted">Администратор «Территории Тепла» свяжется с вами после проверки квалификации.</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($app_error): ?>
            <div class="alert alert-danger rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($app_error) ?>
            </div>
        <?php endif; ?>

        <!-- Сетка мастеров -->
        <?php if (!empty($installers)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                <?php foreach ($installers as $inst): 
                    $photo = !empty($inst['photo_path']) ? $inst['photo_path'] : (!empty($inst['photo']) ? 'images/installers/' . $inst['photo'] : '');
                    $photo = str_replace('../', '', $photo);
                    $hasPhoto = !empty($photo) && file_exists(__DIR__ . '/' . $photo);
                    $avgRating = round($inst['avg_rating'] ?? 0);
                    $portCount = (int)($inst['portfolio_count'] ?? 0);
                ?>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center bg-white d-flex flex-column">
                            <div class="mb-3 mx-auto position-relative" style="width: 120px; height: 120px;">
                                <?php if ($hasPhoto): ?>
                                    <img src="<?= htmlspecialchars($photo) ?>" 
                                         alt="<?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']) ?>" 
                                         class="rounded-circle shadow-sm border border-3 border-warning w-100 h-100 object-fit-cover">
                                <?php else: ?>
                                    <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center shadow-sm border border-3 border-warning w-100 h-100 fs-2 fw-bold">
                                        <?= mb_substr($inst['first_name'], 0, 1) . mb_substr($inst['last_name'], 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <h5 class="fw-bold mb-1 text-dark">
                                <?= htmlspecialchars($inst['first_name'] . ' ' . $inst['last_name']) ?>
                            </h5>
                            
                            <div class="mb-2">
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1 small">
                                    <?= htmlspecialchars($inst['specialization'] ?? 'Монтаж отопления') ?>
                                </span>
                            </div>
                            
                            <!-- Рейтинг и отзывы -->
                            <div class="mb-3 text-warning small">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= $avgRating ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
                                <?php endfor; ?>
                                <span class="text-muted ms-1">(<?= (int)($inst['review_count'] ?? 0) ?> отзыва)</span>
                            </div>

                            <div class="text-muted small mb-3">
                                <?php if (!empty($inst['experience_years'])): ?>
                                    <div class="mb-1"><i class="bi bi-award text-warning me-1"></i> Опыт: <strong><?= (int)$inst['experience_years'] ?> лет</strong></div>
                                <?php endif; ?>
                                <?php if (!empty($inst['city'])): ?>
                                    <div><i class="bi bi-geo-alt text-warning me-1"></i> <?= htmlspecialchars($inst['city']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-auto d-flex gap-2">
                                <a href="installer.php?id=<?= $inst['id'] ?>" class="btn btn-warning flex-fill fw-bold rounded-pill shadow-sm">
                                    Профиль и работы <?= $portCount > 0 ? "($portCount)" : '' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-white mb-5">
                <i class="bi bi-people-fill display-3 text-muted opacity-50 mb-3"></i>
                <h4 class="fw-bold">База мастеров формируется</h4>
                <p class="text-muted mb-0">Скоро здесь появятся контакты сертифицированных монтажников</p>
            </div>
        <?php endif; ?>

        <!-- Блок для новых монтажников (CTA) -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-white" style="background: linear-gradient(135deg, #181c20 0%, #2b3238 100%);">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold mb-2">Для мастеров</span>
                        <h3 class="fw-bold mb-2 text-white">Вы занимаетесь установкой печей, котлов или дымоходов?</h3>
                        <p class="text-white-50 mb-0">
                            Станьте официальным партнером магазина «Территория Тепла». Мы направляем вам прямые заказы от наших клиентов на монтаж оборудования в Майме, Горно-Алтайске и районах республики.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <button type="button" class="btn btn-warning btn-lg fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#installerApplyModal">
                            <i class="bi bi-person-plus-fill me-1"></i> Стать партнером
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Модальное окно подачи заявки мастера -->
<div class="modal fade" id="installerApplyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header py-3" style="background: var(--dark-main, #181c20); color: #fff;">
                <h5 class="modal-title fw-bold" id="applyModalLabel">
                    <i class="bi bi-tools text-warning me-2"></i> Анкета мастера-монтажника
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="apply_installer">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Имя *</label>
                            <input type="text" name="first_name" class="form-control" required placeholder="Алексей">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Фамилия</label>
                            <input type="text" name="last_name" class="form-control" placeholder="Иванов">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Телефон для связи *</label>
                            <input type="tel" name="phone" class="form-control" required placeholder="+7 (999) 000-00-00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Специализация *</label>
                            <select name="specialization" class="form-select" required>
                                <option value="Монтаж печей и каминов">Монтаж печей и каминов</option>
                                <option value="Установка котлов отопления">Установка котлов отопления</option>
                                <option value="Монтаж сэндвич-дымоходов">Монтаж сэндвич-дымоходов</option>
                                <option value="Водяной теплый пол и разводка">Водяной теплый пол и разводка</option>
                                <option value="Комплексный монтаж котельных">Комплексный монтаж котельных</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Опыт работы (лет)</label>
                            <input type="number" name="experience_years" class="form-control" min="0" max="50" value="3">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">Навыки и опыт (кратко)</label>
                            <textarea name="skills" class="form-control" rows="2" placeholder="Наличие инструмента, автомобиля, опыт работы с марками котлов..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="consent_pd" id="inst_pd" required checked>
                                <label class="form-check-label small text-muted" for="inst_pd">
                                    Согласен на обработку персональных данных (152-ФЗ)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">
                        Отправить анкету
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

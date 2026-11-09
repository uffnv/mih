<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: installers.php");
    exit;
}

$installer = getInstallerById($id);
if (!$installer || empty($installer['is_active'])) {
    header("Location: installers.php");
    exit;
}

$msg_success = '';
$msg_error = '';

// Обработка заявки на выезд мастера
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_installation') {
    $client_name = trim($_POST['client_name'] ?? '');
    $client_phone = trim($_POST['client_phone'] ?? '');
    $request_type = $_POST['request_type'] ?? 'installation';
    $address = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $consent = isset($_POST['consent_pd']);

    if (empty($client_name) || empty($client_phone)) {
        $msg_error = 'Пожалуйста, укажите имя и телефон для связи.';
    } elseif (!$consent) {
        $msg_error = 'Необходимо согласие на обработку персональных данных.';
    } else {
        $res = createInstallationRequest([
            'user_id' => $_SESSION['user_id'] ?? null,
            'installer_id' => $id,
            'request_type' => $request_type,
            'description' => $description,
            'address' => $address,
            'client_name' => $client_name,
            'client_phone' => $client_phone,
            'client_email' => $_POST['client_email'] ?? null
        ]);
        if ($res) {
            $msg_success = 'Заявка мастеру успешно отправлена! Он свяжется с вами для согласования времени.';
        } else {
            $msg_error = 'Ошибка при отправке заявки. Пожалуйста, позвоните нам напрямую.';
        }
    }
}

// Обработка отзыва о мастере
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
    if (!isset($_SESSION['user_id'])) {
        $msg_error = 'Для отправки отзыва необходимо войти на сайт.';
    } else {
        $rating = (int)($_POST['rating'] ?? 5);
        $comment = trim($_POST['comment'] ?? '');
        if (!empty($comment)) {
            if (addInstallerReview($id, $_SESSION['user_id'], $rating, $comment)) {
                $msg_success = 'Спасибо за ваш отзыв!';
            } else {
                $msg_error = 'Не удалось сохранить отзыв.';
            }
        }
    }
}

$portfolio = getInstallerPortfolio($id);
$reviews = getInstallerReviews($id);

$page_title = htmlspecialchars($installer['first_name'] . ' ' . $installer['last_name']) . ' — Мастер по монтажу';
require_once __DIR__ . '/includes/header.php';

$photo = !empty($installer['photo_path']) ? $installer['photo_path'] : (!empty($installer['photo']) ? 'images/installers/' . $installer['photo'] : '');
$photo = str_replace('../', '', $photo);
$hasPhoto = !empty($photo) && file_exists(__DIR__ . '/' . $photo);

// Средний рейтинг мастера
$avgRating = 0;
if (!empty($reviews)) {
    $sum = array_sum(array_column($reviews, 'rating'));
    $avgRating = round($sum / count($reviews), 1);
}
?>

<main class="py-4 py-lg-5" style="background-color: var(--bg-page, #f8fafc);">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item"><a href="installers.php" class="text-decoration-none text-muted">Монтажники</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                    <?= htmlspecialchars($installer['first_name'] . ' ' . $installer['last_name']) ?>
                </li>
            </ol>
        </nav>

        <?php if ($msg_success): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($msg_success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($msg_error): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($msg_error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Левая колонка - Карточка мастера -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white mb-4 sticky-lg-top" style="top: 85px; z-index: 10;">
                    <div class="mb-3 mx-auto" style="width: 140px; height: 140px;">
                        <?php if ($hasPhoto): ?>
                            <img src="<?= htmlspecialchars($photo) ?>" 
                                 class="rounded-circle shadow-sm border border-3 border-warning w-100 h-100 object-fit-cover" 
                                 alt="<?= htmlspecialchars($installer['first_name']) ?>">
                        <?php else: ?>
                            <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center shadow-sm border border-3 border-warning w-100 h-100 fs-1 fw-bold">
                                <?= mb_substr($installer['first_name'], 0, 1) . mb_substr($installer['last_name'], 0, 1) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <h4 class="fw-bold mb-1 text-dark">
                        <?= htmlspecialchars($installer['first_name'] . ' ' . $installer['last_name']) ?>
                    </h4>
                    
                    <div class="mb-2">
                        <span class="badge bg-warning text-dark px-3 py-1">
                            <?= htmlspecialchars($installer['specialization'] ?? 'Мастер по монтажу') ?>
                        </span>
                    </div>

                    <!-- Рейтинг -->
                    <div class="mb-3 text-warning">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= round($avgRating) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
                        <?php endfor; ?>
                        <span class="text-dark fw-bold ms-1"><?= $avgRating > 0 ? $avgRating : '—' ?></span>
                        <span class="text-muted small">(<?= count($reviews) ?>)</span>
                    </div>

                    <hr class="my-3">

                    <div class="text-start small mb-4">
                        <?php if (!empty($installer['city'])): ?>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-warning fs-6"></i>
                                <span><strong>Город/Район:</strong> <?= htmlspecialchars($installer['city']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($installer['experience_years'])): ?>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-award-fill text-warning fs-6"></i>
                                <span><strong>Стаж работы:</strong> <?= htmlspecialchars($installer['experience_years']) ?> лет</span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($installer['phone'])): ?>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-telephone-fill text-warning fs-6"></i>
                                <span><strong>Телефон:</strong> 
                                    <a href="tel:<?= htmlspecialchars($installer['phone']) ?>" class="text-dark text-decoration-none fw-bold">
                                        <?= htmlspecialchars($installer['phone']) ?>
                                    </a>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="button" class="btn btn-warning w-100 py-3 fw-bold rounded-pill shadow-sm mb-2" data-bs-toggle="modal" data-bs-target="#requestModal">
                        <i class="bi bi-calendar-check me-1"></i> Пригласить мастера
                    </button>
                </div>
            </div>
            
            <!-- Правая колонка - Описание, Портфолио, Отзывы -->
            <div class="col-lg-8">
                <!-- О мастере -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="bi bi-info-circle-fill text-warning me-2"></i> О мастере и квалификации
                    </h5>
                    <div class="text-muted lh-lg">
                        <?= !empty($installer['description']) ? nl2br(htmlspecialchars($installer['description'])) : 'Сертифицированный партнер магазина «Территория Тепла». Выполняет квалифицированную установку банных и отопительных печей, сборку сэндвич-дымоходов с соблюдением всех противопожарных отступок СНиП, опрессовку и подключение водяных контуров.' ?>
                    </div>
                </div>

                <!-- Портфолио работ -->
                <?php if (!empty($portfolio)): ?>
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                            <i class="bi bi-images text-warning me-2"></i> Фотографии выполненных объектов (<?= count($portfolio) ?>)
                        </h5>
                        <div class="row g-3">
                            <?php foreach ($portfolio as $item): 
                                $pImg = str_replace('../', '', $item['image_path']);
                            ?>
                                <div class="col-6 col-md-4">
                                    <div class="ratio ratio-4x3 rounded-3 overflow-hidden border shadow-sm">
                                        <img src="<?= htmlspecialchars($pImg) ?>" class="w-100 h-100 object-fit-cover" alt="Монтажный объект">
                                    </div>
                                    <?php if (!empty($item['title'])): ?>
                                        <small class="text-muted d-block mt-1 text-truncate"><?= htmlspecialchars($item['title']) ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Отзывы -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-chat-quote-fill text-warning me-2"></i> Отзывы заказчиков (<?= count($reviews) ?>)
                        </h5>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button type="button" class="btn btn-outline-dark btn-sm rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                <i class="bi bi-pencil me-1"></i> Оставить отзыв
                            </button>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-secondary btn-sm rounded-pill">Войти для отзыва</a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($reviews)): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($reviews as $rev): 
                                $author = !empty($rev['first_name']) ? ($rev['first_name'] . ' ' . $rev['last_name']) : ($rev['username'] ?? 'Заказчик');
                            ?>
                                <div class="p-3 bg-light rounded-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($author) ?></div>
                                        <div class="text-warning small">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?= $i <= $rev['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>' ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="mb-1 text-muted small"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <?= date('d.m.Y', strtotime($rev['created_at'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted small">
                            У этого мастера пока нет опубликованных отзывов. Будьте первыми!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Модалка заявки на выезд мастера -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header py-3" style="background: var(--dark-main, #181c20); color: #fff;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-calendar-event text-warning me-2"></i> Пригласить мастера на замер / монтаж
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="request_installation">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Ваше имя *</label>
                        <input type="text" name="client_name" class="form-control" required placeholder="Иван">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Контактный телефон *</label>
                        <input type="tel" name="client_phone" class="form-control" required placeholder="+7 (___) ___-__-__">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Тип работ *</label>
                        <select name="request_type" class="form-select">
                            <option value="installation">Монтаж нового оборудования</option>
                            <option value="consultation">Выезд на замер и консультация</option>
                            <option value="repair">Ремонт / замена узлов</option>
                            <option value="maintenance">Чистка дымохода / обслуживание</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Адрес объекта (город/село, улица)</label>
                        <input type="text" name="address" class="form-control" placeholder="с. Майма, ул. ...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Описание задачи</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Какая печь или котел, высота дымохода, готовность помещения..."></textarea>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="consent_pd" id="req_pd" required checked>
                        <label class="form-check-label small text-muted" for="req_pd">
                            Согласен на обработку персональных данных (152-ФЗ)
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">
                        Отправить заявку
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модалка отзыва -->
<?php if (isset($_SESSION['user_id'])): ?>
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header py-3" style="background: var(--dark-main, #181c20); color: #fff;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-star-fill text-warning me-2"></i> Оставить отзыв о мастере
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_review">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Ваша оценка</label>
                        <select name="rating" class="form-select">
                            <option value="5">★★★★★ (5 — Отлично)</option>
                            <option value="4">★★★★☆ (4 — Хорошо)</option>
                            <option value="3">★★★☆☆ (3 — Нормально)</option>
                            <option value="2">★★☆☆☆ (2 — Плохо)</option>
                            <option value="1">★☆☆☆☆ (1 — Ужасно)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Ваш отзыв</label>
                        <textarea name="comment" class="form-control" rows="3" required placeholder="Качество работы, соблюдение сроков, аккуратность..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">
                        Опубликовать отзыв
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

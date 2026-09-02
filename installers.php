<?php
session_start();
require_once __DIR__ . '/includes/config.php';

$page_title = 'Наши монтажники — Михаил Потапыч';
require_once __DIR__ . '/includes/header.php';

// Получаем список мастеров
$stmt = $pdo->query("
    SELECT 
        i.*,
        (SELECT AVG(rating) FROM installer_reviews WHERE installer_id = i.id) as avg_rating,
        (SELECT COUNT(*) FROM installer_reviews WHERE installer_id = i.id) as review_count
    FROM installers i 
    WHERE i.is_active = 1
    ORDER BY avg_rating DESC, id ASC
");
$installers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="bg-light">
    <!-- Hero заголовок -->
    <div class="hero-gradient text-white py-5">
        <div class="container text-center py-3">
            <h1 class="display-5 fw-bold text-uppercase mb-2">Наши <span class="text-warning">Мастера</span></h1>
            <p class="lead text-white-50 mb-0">Сертифицированные специалисты по установке и обслуживанию отопления</p>
        </div>
    </div>

    <div class="container py-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Монтажники</li>
            </ol>
        </nav>

        <?php if (!empty($installers)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($installers as $inst): 
                    $photo = !empty($inst['photo']) ? 'images/installers/' . $inst['photo'] : '';
                    if (!empty($inst['photo_path'])) $photo = $inst['photo_path'];
                    $photo = str_replace('../', '', $photo);
                    $hasPhoto = !empty($photo) && file_exists(__DIR__ . '/' . $photo);
                    $avgRating = round($inst['avg_rating'] ?? 0);
                ?>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center card-hover bg-white">
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
                            
                            <p class="text-muted small mb-2"><?= htmlspecialchars($inst['specialization'] ?? 'Специалист по монтажу') ?></p>
                            
                            <!-- Рейтинг -->
                            <div class="mb-3 text-warning small">
                                <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $avgRating ? '<i class="bi bi-star-fill"></i> ' : '<i class="bi bi-star"></i> ';
                                    }
                                ?>
                                <span class="text-muted ms-1">(<?= (int)($inst['review_count'] ?? 0) ?>)</span>
                            </div>

                            <?php if (!empty($inst['city'])): ?>
                                <div class="small text-muted mb-3">
                                    <i class="bi bi-geo-alt text-warning me-1"></i><?= htmlspecialchars($inst['city']) ?>
                                </div>
                            <?php endif; ?>

                            <a href="installer.php?id=<?= $inst['id'] ?>" class="btn btn-warning w-100 fw-bold rounded-pill shadow-sm mt-auto">
                                Подробнее о мастере
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-white">
                <i class="bi bi-people-fill display-3 text-muted opacity-50 mb-3"></i>
                <h4 class="fw-bold">Мастера не найдены</h4>
                <p class="text-muted mb-0">Список монтажников сейчас обновляется</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

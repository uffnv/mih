<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = htmlspecialchars($installer['first_name'] . ' ' . $installer['last_name']) . ' — Мастер';
require_once __DIR__ . '/includes/header.php';

$photo = !empty($installer['photo']) ? 'images/installers/' . $installer['photo'] : '';
if (!empty($installer['photo_path'])) $photo = $installer['photo_path'];
$photo = str_replace('../', '', $photo);
$hasPhoto = !empty($photo) && file_exists(__DIR__ . '/' . $photo);
?>

<main class="py-4 py-lg-5 bg-light">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php">Главная</a></li>
                <li class="breadcrumb-item"><a href="installers.php">Монтажники</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($installer['first_name'] . ' ' . $installer['last_name']) ?></li>
            </ol>
        </nav>

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
                    
                    <h4 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($installer['first_name'] . ' ' . $installer['last_name']) ?></h4>
                    <p class="text-muted small mb-3"><?= htmlspecialchars($installer['specialization'] ?? 'Мастер по монтажу') ?></p>
                    
                    <hr class="my-3">

                    <div class="text-start small">
                        <?php if (!empty($installer['city'])): ?>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-warning fs-6"></i>
                                <span><strong>Город:</strong> <?= htmlspecialchars($installer['city']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($installer['experience_years'])): ?>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-award-fill text-warning fs-6"></i>
                                <span><strong>Опыт работы:</strong> <?= htmlspecialchars($installer['experience_years']) ?> лет</span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($installer['phone'])): ?>
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-telephone-fill text-warning fs-6"></i>
                                <span><strong>Телефон:</strong> <?= htmlspecialchars($installer['phone']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Правая колонка - Описание, Портфолио, Отзывы -->
            <div class="col-lg-8">
                <!-- О мастере -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="bi bi-info-circle-fill text-warning me-2"></i>О специалисте
                    </h5>
                    <div class="text-muted lh-lg">
                        <?= !empty($installer['description']) ? nl2br(htmlspecialchars($installer['description'])) : 'Информация о мастере уточняется.' ?>
                    </div>
                </div>

                <!-- Портфолио работ -->
                <?php if (!empty($portfolio)): ?>
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                            <i class="bi bi-images text-warning me-2"></i>Примеры выполненных работ
                        </h5>
                        <div class="row g-2">
                            <?php foreach ($portfolio as $item): 
                                $pImg = str_replace('../', '', $item['image_path']);
                            ?>
                                <div class="col-6 col-md-4">
                                    <a href="<?= htmlspecialchars($pImg) ?>" class="glightbox" data-gallery="installer-portfolio">
                                        <div class="ratio ratio-4x3 rounded-3 overflow-hidden border card-hover">
                                            <img src="<?= htmlspecialchars($pImg) ?>" class="w-100 h-100 object-fit-cover" alt="Работа">
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Отзывы -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-chat-quote-fill text-warning me-2"></i>Отзывы клиентов</span>
                        <span class="badge bg-warning text-dark"><?= count($reviews) ?></span>
                    </h5>

                    <?php if ($msg): ?>
                        <div class="alert alert-success rounded-3 mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <span><?= htmlspecialchars($msg) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $rev): ?>
                                <div class="p-3 bg-light rounded-3 mb-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="text-dark"><?= htmlspecialchars($rev['first_name'] ? ($rev['first_name'] . ' ' . $rev['last_name']) : $rev['username']) ?></strong>
                                        <div class="text-warning small">
                                            <?php 
                                                for ($i = 1; $i <= 5; $i++) {
                                                    echo $i <= $rev['rating'] ? '<i class="bi bi-star-fill"></i> ' : '<i class="bi bi-star"></i> ';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                    <p class="mb-0 text-muted small"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small py-3">Отзывов пока нет. Будьте первым, кто оставит отзыв о мастере!</p>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" class="p-3 bg-light rounded-4 border">
                            <h6 class="fw-bold mb-3 text-dark">Оставить отзыв о мастере</h6>
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Ваша оценка:</label>
                                <select name="rating" class="form-select form-select-sm w-auto rounded-pill bg-white">
                                    <option value="5">★★★★★ 5 - Отлично</option>
                                    <option value="4">★★★★☆ 4 - Хорошо</option>
                                    <option value="3">★★★☆☆ 3 - Нормально</option>
                                    <option value="2">★★☆☆☆ 2 - Плохо</option>
                                    <option value="1">★☆☆☆☆ 1 - Ужасно</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea name="comment" class="form-control bg-white" rows="3" required placeholder="Опишите ваши впечатления от выполненной работы..."></textarea>
                            </div>
                            <button name="submit_review" class="btn btn-warning fw-bold px-4 rounded-pill shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> Опубликовать отзыв
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <small class="text-muted">Чтобы оставить отзыв, пожалуйста, <a href="login.php" class="fw-bold text-dark hover-warning">войдите в аккаунт</a>.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

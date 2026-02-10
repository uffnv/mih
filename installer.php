<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { echo "<script>location.href='installers.php';</script>"; exit; }

$stmt = $pdo->prepare("SELECT * FROM installers WHERE id = ?");
$stmt->execute([$id]);
$installer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$installer) { echo "<div class='container py-5'>Мастер не найден</div>"; exit; }

$stmt = $pdo->prepare("SELECT * FROM installer_portfolio WHERE installer_id = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$portfolio = $stmt->fetchAll();

// Обработка отзыва
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) $msg = "Нужна авторизация";
    else {
        $r = (int)$_POST['rating'];
        $c = trim($_POST['comment']);
        // Проверка рейтинга
        if($r < 1) $r = 1; 
        if($r > 5) $r = 5;

        $pdo->prepare("INSERT INTO installer_reviews (installer_id, user_id, rating, comment, created_at) VALUES (?,?,?,?, NOW())")->execute([$id, $_SESSION['user_id'], $r, $c]);
        $msg = "Отзыв добавлен!";
    }
}

// Отзывы
$stmt = $pdo->prepare("SELECT r.*, u.username, u.first_name, u.last_name FROM installer_reviews r JOIN users u ON r.user_id = u.id WHERE r.installer_id = ? ORDER BY r.created_at DESC");
$stmt->execute([$id]);
$reviews = $stmt->fetchAll();
?>

<main class="py-5 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="installers.php" class="text-dark">Мастера</a></li>
                <!-- ИСПРАВЛЕНО: Имя Фамилия -->
                <li class="breadcrumb-item active"><?= htmlspecialchars($installer['first_name'] . ' ' . $installer['last_name']) ?></li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <?php if($installer['photo']): ?>
                            <img src="images/installers/<?= $installer['photo'] ?>" class="rounded-circle mb-3" width="150" height="150" style="object-fit:cover">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto text-white" style="width:150px;height:150px;font-size:3rem">
                                <?= mb_substr($installer['first_name'], 0, 1) ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- ИСПРАВЛЕНО: Имя Фамилия -->
                        <h4 class="fw-bold"><?= htmlspecialchars($installer['first_name'] . ' ' . $installer['last_name']) ?></h4>
                        
                        <p class="text-muted"><?= htmlspecialchars($installer['specialization']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5>О мастере</h5>
                        <p><?= nl2br(htmlspecialchars($installer['description'])) ?></p>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5>Отзывы (<?= count($reviews) ?>)</h5>
                        <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
                        
                        <div class="mb-4">
                            <?php foreach($reviews as $rev): ?>
                                <div class="border-bottom pb-2 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= htmlspecialchars($rev['first_name'] ? $rev['first_name'] : $rev['username']) ?></strong>
                                        <div class="text-warning"><?= str_repeat('★', $rev['rating']) ?></div>
                                    </div>
                                    <p class="mb-0 text-muted"><?= htmlspecialchars($rev['comment']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if(isset($_SESSION['user_id'])): ?>
                            <form method="POST" class="bg-light p-3 rounded">
                                <h6 class="fw-bold">Оставить отзыв</h6>
                                <!-- ИСПРАВЛЕНО: Все оценки от 1 до 5 -->
                                <select name="rating" class="form-select mb-2 w-auto">
                                    <option value="5">5 - Отлично</option>
                                    <option value="4">4 - Хорошо</option>
                                    <option value="3">3 - Нормально</option>
                                    <option value="2">2 - Плохо</option>
                                    <option value="1">1 - Ужасно</option>
                                </select>
                                <textarea name="comment" class="form-control mb-2" required placeholder="Ваш отзыв"></textarea>
                                <button name="submit_review" class="btn btn-warning fw-bold">Отправить</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

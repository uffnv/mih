<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/admin_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$promo = [
    'title' => '',
    'description' => '',
    'image_path' => '',
    'discount_percent' => '',
    'start_date' => date('Y-m-d H:i:s'),
    'end_date' => '',
    'is_active' => 1,
    'sort_order' => 0
];

// Загрузка данных если редактирование
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM promotions WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($found) {
        $promo = $found;
        // Приводим дату к формату для input datetime-local, если нужно (тут просто date input)
    }
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $discount_percent = !empty($_POST['discount_percent']) ? (int)$_POST['discount_percent'] : null;
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d H:i:s');
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];

    // Загрузка фото (Защищенная валидация)
    $image_path = $promo['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $up = safeUploadFile($_FILES['image'], '../images/promotions/', ['jpg', 'jpeg', 'png', 'webp'], 'promo_');
        if ($up['success']) {
            $image_path = '/images/promotions/' . $up['filename'];
        } else {
            $error = $up['error'];
        }
    }

    try {
        if ($id) {
            // Обновление
            $sql = "UPDATE promotions SET 
                    title = ?, description = ?, image_path = ?, discount_percent = ?, 
                    start_date = ?, end_date = ?, is_active = ?, sort_order = ? 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $image_path, $discount_percent, $start_date, $end_date, $is_active, $sort_order, $id]);
        } else {
            // Создание
            $sql = "INSERT INTO promotions (title, description, image_path, discount_percent, start_date, end_date, is_active, sort_order) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $image_path, $discount_percent, $start_date, $end_date, $is_active, $sort_order]);
        }
        
        echo "<script>window.location.href='promotions.php';</script>";
        exit;
    } catch (Exception $e) {
        $error = "Ошибка сохранения: " . $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $id ? 'Редактирование акции' : 'Новая акция' ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="promotions.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Назад к списку
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="row g-3">
    <?= getCsrfField(); ?>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header fw-bold">Основная информация</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="title" class="form-label">Заголовок акции <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($promo['title']) ?>">
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($promo['description']) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header fw-bold">Настройки</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Статус</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= $promo['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Активна</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="discount_percent" class="form-label">Размер скидки (%)</label>
                    <input type="number" class="form-control" id="discount_percent" name="discount_percent" min="0" max="100" value="<?= htmlspecialchars($promo['discount_percent']) ?>">
                    <div class="form-text">Отображается на бейджике (необязательно)</div>
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label">Порядок сортировки</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= htmlspecialchars($promo['sort_order']) ?>">
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">Дата начала</label>
                    <!-- Используем datetime-local для точности или просто date -->
                    <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                           value="<?= date('Y-m-d\TH:i', strtotime($promo['start_date'])) ?>">
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">Дата окончания</label>
                    <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                           value="<?= $promo['end_date'] ? date('Y-m-d\TH:i', strtotime($promo['end_date'])) : '' ?>">
                    <div class="form-text">Оставьте пустым, если бессрочно</div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header fw-bold">Изображение</div>
            <div class="card-body text-center">
                <?php if (!empty($promo['image_path'])): ?>
                    <img src="<?= htmlspecialchars($promo['image_path']) ?>" class="img-fluid mb-3 rounded" style="max-height: 200px;">
                <?php endif; ?>
                
                <input type="file" class="form-control" name="image" accept="image/*">
            </div>
        </div>
    </div>

    <div class="col-12 text-end mb-5">
        <button type="submit" class="btn btn-success btn-lg px-5">
            <i class="bi bi-save"></i> Сохранить
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/admin_footer.php'; ?>

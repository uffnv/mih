<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// --- НАЧАЛО ЛОГИКИ ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$product = null;
$images = [];

// 1. Логика удаления изображения (с редиректом)
if (isset($_GET['delete_image'])) {
    if (!verifyCsrfToken($_GET['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }
    $imgId = (int)$_GET['delete_image'];
    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE id = ?");
    $stmt->execute([$imgId]);
    $img = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($img && !empty($img['image_path'])) {
        $realFile = realpath(__DIR__ . '/../' . ltrim($img['image_path'], '/'));
        $allowedDir = realpath(__DIR__ . '/../images/products');
        if ($realFile && $allowedDir && strpos($realFile, $allowedDir) === 0 && file_exists($realFile)) {
            @unlink($realFile);
        }
    }
    $pdo->prepare("DELETE FROM product_images WHERE id = ?")->execute([$imgId]);
    
    // Редирект сработает, так как HTML еще не выведен
    header("Location: product_edit.php?id=$id");
    exit;
}

// 2. Обработка сохранения формы (с редиректом)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }
    $data = [
        'name' => $_POST['name'],
        'category_id' => $_POST['category_id'],
        'price' => $_POST['price'],
        'old_price' => $_POST['old_price'] ?: 0,
        'sku' => $_POST['sku'],
        'stock_quantity' => $_POST['stock_quantity'],
        'description' => $_POST['description'],
        'short_description' => $_POST['short_description'],
        'warranty_months' => $_POST['warranty_months'] ?? 12,
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'installation_required' => isset($_POST['installation_required']) ? 1 : 0
    ];

    if ($id) {
        // Функция updateProduct должна быть определена в functions.php
        // Если ее нет, замените на прямой SQL UPDATE
        if (function_exists('updateProduct')) {
             updateProduct($id, $data); 
        } else {
             // Фолбэк, если функции нет (примерный код, можно убрать если уверены в функции)
             // ... код прямого обновления ...
        }
    } else {
        // Функция addProduct должна быть определена в functions.php
        if (function_exists('addProduct')) {
            $id = addProduct($data);
        }
    }

    // Загрузка изображений (Защищенная валидация)
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $name,
                    'tmp_name' => $_FILES['images']['tmp_name'][$key],
                    'type' => $_FILES['images']['type'][$key],
                    'error' => 0,
                    'size' => $_FILES['images']['size'][$key]
                ];
                
                $uploadResult = safeUploadFile($file, '../images/products/', ['jpg', 'jpeg', 'png', 'webp', 'svg'], 'prod_');
                if ($uploadResult['success']) {
                    $db_path = 'images/products/' . $uploadResult['filename'];
                    $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                    $stmt->execute([$id, $db_path]);
                }
            }
        }
    }

    header("Location: products.php");
    exit;
}

// 3. Получение данных для отображения (если не было редиректа)
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Получаем изображения
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получаем категории для списка
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
// --- КОНЕЦ ЛОГИКИ ---

// Теперь безопасно подключаем шапку
require_once 'admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $id ? 'Редактирование товара' : 'Новый товар'; ?></h1>
    <a href="products.php" class="btn btn-secondary">Назад</a>
</div>

<form method="POST" enctype="multipart/form-data">
    <?= getCsrfField(); ?>
    <div class="row g-4">
        <!-- Левая колонка (Основное) -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Название товара</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Краткое описание</label>
                        <textarea name="short_description" class="form-control" rows="2"><?= htmlspecialchars($product['short_description'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Полное описание</label>
                        <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Изображения -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Изображения</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Загрузить новые фото</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    </div>
                    
                    <?php if (!empty($images)): ?>
                        <div class="row g-3 mt-2">
                            <?php foreach ($images as $img): ?>
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="card h-100 shadow-sm">
                                        <img src="../<?= htmlspecialchars($img['image_path']); ?>" class="card-img-top object-fit-cover" style="height: 100px;" alt="Product">
                                        <div class="card-footer p-1 text-center">
                                            <a href="product_edit.php?id=<?= $id; ?>&delete_image=<?= $img['id']; ?>&csrf_token=<?= generateCsrfToken(); ?>" class="text-danger small text-decoration-none" onclick="return confirm('Удалить фото?');">
                                                <i class="bi bi-trash"></i> Удалить
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Правая колонка (Настройки) -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Параметры</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Артикул (SKU)</label>
                        <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($product['sku'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Категория</label>
                        <select name="category_id" class="form-select">
                            <option value="">Без категории</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id']; ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Цена</label>
                            <input type="number" name="price" class="form-control" value="<?= $product['price'] ?? ''; ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Старая цена</label>
                            <input type="number" name="old_price" class="form-control" value="<?= $product['old_price'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Остаток</label>
                            <input type="number" name="stock_quantity" class="form-control" value="<?= $product['stock_quantity'] ?? 0; ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Гарантия (мес)</label>
                            <input type="number" name="warranty_months" class="form-control" value="<?= $product['warranty_months'] ?? 12; ?>">
                        </div>
                    </div>

                    <hr>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="activeSwitch" <?= ($product['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="activeSwitch">Активен (на сайте)</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="featSwitch" <?= ($product['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="featSwitch">Хит продаж</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="installation_required" id="installSwitch" <?= ($product['installation_required'] ?? 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="installSwitch">Требует монтажа</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php require_once 'admin_footer.php'; ?>

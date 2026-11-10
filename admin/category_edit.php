<?php
require_once __DIR__ . '/admin_auth.php';
require_once '../includes/config.php';

// --- НАЧАЛО: Логика перенесена вверх ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Получение данных категории
$category = $id ? $pdo->prepare("SELECT * FROM categories WHERE id = ?") : null;
if ($category) {
    $category->execute([$id]);
    $category = $category->fetch();
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']) ?: strtolower(str_replace(' ', '-', $name)); // Простой авто-слаг
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
    }
    
    // Теперь редирект сработает, так как HTML еще не выводился
    header("Location: categories.php");
    exit;
}
// --- КОНЕЦ: Логика завершена ---

// Теперь безопасно подключаем шапку (вывод HTML)
require_once 'admin_header.php'; 
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $id ? 'Редактировать категорию' : 'Новая категория'; ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="categories.php" class="btn btn-secondary">Назад</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <?= getCsrfField(); ?>
            <div class="mb-3">
                <label class="form-label">Название</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Slug (URL)</label>
                <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($category['slug'] ?? ''); ?>" placeholder="Оставьте пустым для автогенерации">
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

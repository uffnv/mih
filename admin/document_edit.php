<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// --- ЛОГИКА ПЕРЕНЕСЕНА ВВЕРХ ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$doc = null;

// 1. Получаем данные документа (если редактируем)
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM company_documents WHERE id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 2. Обрабатываем отправку формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }
    $title = trim($_POST['title']);
    
    // Обработка файла
    if (!empty($_FILES['file']['name'])) {
        $up = safeUploadFile($_FILES['file'], '../images/documents/', ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'], 'doc_', false);
        if ($up['success']) {
            $filePath = 'images/documents/' . $up['filename'];
            
            if ($id) {
                // Обновление с файлом
                $stmt = $pdo->prepare("UPDATE company_documents SET title = ?, file_path = ? WHERE id = ?");
                $stmt->execute([$title, $filePath, $id]);
            } else {
                // Добавление нового
                $stmt = $pdo->prepare("INSERT INTO company_documents (title, file_path) VALUES (?, ?)");
                $stmt->execute([$title, $filePath]);
            }
            header("Location: documents.php");
            exit;
        } else {
            $error = $up['error'];
        }
    } elseif ($id) {
        // Обновление только названия (без файла)
        $stmt = $pdo->prepare("UPDATE company_documents SET title = ? WHERE id = ?");
        $stmt->execute([$title, $id]);
        header("Location: documents.php");
        exit;
    } else {
        $error = "Пожалуйста, выберите файл.";
    }
}
// --- КОНЕЦ ЛОГИКИ ---

// Теперь безопасно подключаем шапку (вывод HTML)
require_once 'admin_header.php'; 
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $id ? 'Редактировать документ' : 'Добавить документ'; ?></h1>
    <a href="documents.php" class="btn btn-secondary">Назад</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= getCsrfField(); ?>
            <div class="mb-3">
                <label class="form-label">Название</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($doc['title'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Файл</label>
                <input type="file" name="file" class="form-control">
                <div class="form-text">Поддерживаются: jpg, png, pdf, doc.</div>
                
                <?php if (!empty($doc['file_path'])): ?>
                    <div class="mt-2">
                        <span class="badge bg-info text-dark">Текущий файл:</span>
                        <a href="../<?= htmlspecialchars($doc['file_path']); ?>" target="_blank" class="ms-2 text-decoration-none">
                            <?= basename($doc['file_path']); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary"><?= $id ? 'Сохранить изменения' : 'Загрузить'; ?></button>
        </form>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

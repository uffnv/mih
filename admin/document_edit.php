<?php
require_once __DIR__ . '/admin_auth.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Автопроверка колонок
try {
    $docCols = $pdo->query("SHOW COLUMNS FROM company_documents")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('category', $docCols)) {
        $pdo->exec("ALTER TABLE company_documents ADD COLUMN category VARCHAR(100) NOT NULL DEFAULT 'Сертификаты';");
    }
    if (!in_array('file_size', $docCols)) {
        $pdo->exec("ALTER TABLE company_documents ADD COLUMN file_size INT(11) NULL DEFAULT 0;");
    }
} catch (Throwable $e) {}

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$doc = null;
$error = null;
$success = null;

// Получаем данные документа при редактировании
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM company_documents WHERE id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }

    $upload_mode = $_POST['upload_mode'] ?? 'single';
    $category = trim($_POST['category'] ?? 'Сертификаты');

    if ($upload_mode === 'batch') {
        // --- ПАКЕТНАЯ ЗАГРУЗКА НЕСКОЛЬКИХ ДОКУМЕНТОВ ---
        $uploaded_count = 0;
        $failed_count = 0;

        if (!empty($_FILES['batch_files']['name'][0])) {
            $total_files = count($_FILES['batch_files']['name']);
            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['batch_files']['error'][$i] === UPLOAD_ERR_OK) {
                    $itemFile = [
                        'name' => $_FILES['batch_files']['name'][$i],
                        'type' => $_FILES['batch_files']['type'][$i],
                        'tmp_name' => $_FILES['batch_files']['tmp_name'][$i],
                        'error' => $_FILES['batch_files']['error'][$i],
                        'size' => $_FILES['batch_files']['size'][$i]
                    ];

                    $up = safeUploadFile($itemFile, '../images/documents/', ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'], 'doc_', false);
                    if ($up['success']) {
                        $filePath = 'images/documents/' . $up['filename'];
                        // Формируем красивое название из имени файла
                        $origName = pathinfo($itemFile['name'], PATHINFO_FILENAME);
                        $cleanTitle = trim(preg_replace('/[_\-]+/', ' ', $origName));
                        $cleanTitle = mb_strtoupper(mb_substr($cleanTitle, 0, 1)) . mb_substr($cleanTitle, 1);

                        $custom_prefix = trim($_POST['batch_title_prefix'] ?? '');
                        if (!empty($custom_prefix)) {
                            $docTitle = $custom_prefix . ' — ' . $cleanTitle;
                        } else {
                            $docTitle = $cleanTitle ?: 'Документ качества';
                        }

                        $stmt = $pdo->prepare("INSERT INTO company_documents (title, file_path, category, file_size, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $stmt->execute([$docTitle, $filePath, $category, (int)$itemFile['size']]);
                        $uploaded_count++;
                    } else {
                        $failed_count++;
                    }
                }
            }

            if ($uploaded_count > 0) {
                header("Location: documents.php?msg=batch_success&count=" . $uploaded_count);
                exit;
            } else {
                $error = "Не удалось загрузить файлы. Убедитесь, что расширения разрешены (PDF, JPG, PNG, DOCX).";
            }
        } else {
            $error = "Пожалуйста, выберите хотя бы один файл для пакетной загрузки.";
        }
    } else {
        // --- ОДИНОЧНАЯ ЗАГРУЗКА / РЕДАКТИРОВАНИЕ ---
        $title = trim($_POST['title'] ?? '');
        if (empty($title)) {
            $error = "Укажите название документа.";
        } else {
            if (!empty($_FILES['file']['name'])) {
                $up = safeUploadFile($_FILES['file'], '../images/documents/', ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'], 'doc_', false);
                if ($up['success']) {
                    $filePath = 'images/documents/' . $up['filename'];
                    $fileSize = (int)$_FILES['file']['size'];

                    if ($id) {
                        $stmt = $pdo->prepare("UPDATE company_documents SET title = ?, file_path = ?, category = ?, file_size = ? WHERE id = ?");
                        $stmt->execute([$title, $filePath, $category, $fileSize, $id]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO company_documents (title, file_path, category, file_size, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $stmt->execute([$title, $filePath, $category, $fileSize]);
                    }
                    header("Location: documents.php?msg=saved");
                    exit;
                } else {
                    $error = $up['error'];
                }
            } elseif ($id) {
                // Обновление без смены файла
                $stmt = $pdo->prepare("UPDATE company_documents SET title = ?, category = ? WHERE id = ?");
                $stmt->execute([$title, $category, $id]);
                header("Location: documents.php?msg=saved");
                exit;
            } else {
                $error = "Пожалуйста, выберите файл документа.";
            }
        }
    }
}

$page_title = $id ? 'Редактировать документ' : 'Загрузка документов в обработку';
require_once 'admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0"><?= $id ? 'Редактирование документа' : 'Загрузка документов в обработку' ?></h1>
        <small class="text-muted">Пакетный и штучный ввод сертификатов, паспортов оборудования и лицензий</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="documents.php" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Назад к списку
        </a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <?php if (!$id): ?>
            <!-- Переключатель режима для новой загрузки -->
            <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded-pill border" id="uploadTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill fw-bold" id="batch-tab" data-bs-toggle="pill" data-bs-target="#batch-pane" type="button" role="tab">
                        <i class="bi bi-collection me-1"></i> Пакетная загрузка (несколько файлов сразу)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold" id="single-tab" data-bs-toggle="pill" data-bs-target="#single-pane" type="button" role="tab">
                        <i class="bi bi-file-earmark-plus me-1"></i> Одиночный документ
                    </button>
                </li>
            </ul>
        <?php endif; ?>

        <div class="tab-content" id="uploadTabContent">
            <!-- Вкладка 1: Пакетная загрузка (Multiple) -->
            <?php if (!$id): ?>
            <div class="tab-pane fade show active" id="batch-pane" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-cloud-arrow-up-fill text-warning me-2"></i> Массовая загрузка документов
                    </h5>
                    <p class="text-muted small mb-4">
                        Выберите несколько сканов, паспортов или PDF-файлов одновременно. Система сама очистит имена файлов и структурирует их в каталог.
                    </p>

                    <form method="POST" action="document_edit.php" enctype="multipart/form-data">
                        <?= getCsrfField(); ?>
                        <input type="hidden" name="upload_mode" value="batch">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Категория для пакета:</label>
                            <select name="category" class="form-select">
                                <option value="Сертификаты соответствия">Сертификаты соответствия (ГОСТ / Пожарные)</option>
                                <option value="Паспорта и инструкции">Паспорта и инструкции оборудования</option>
                                <option value="Дилерские соглашения">Дилерские соглашения и сертификаты дилера</option>
                                <option value="Лицензии и допуски">Лицензии, допуски СРО и свидетельства</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Общий префикс в названии (необязательно):</label>
                            <input type="text" name="batch_title_prefix" class="form-control" placeholder="Например: Сертификат ZOTA или Паспорт печи">
                            <div class="form-text">Если указан, каждое название будет выглядеть как «Префикс — Имя файла».</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Выберите файлы для обработки:</label>
                            <div class="p-4 border border-2 border-dashed rounded-3 text-center bg-light">
                                <i class="bi bi-folder2-open display-4 text-warning opacity-75 d-block mb-2"></i>
                                <input type="file" name="batch_files[]" multiple required class="form-control mb-2" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
                                <small class="text-muted d-block">Зажмите Ctrl / Shift, чтобы выбрать сразу 5, 10 или 20 файлов (PDF, DOCX, JPG, PNG).</small>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold rounded-pill shadow-sm">
                                <i class="bi bi-upload me-1"></i> Обработать и загрузить весь пакет
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Вкладка 2: Одиночный документ / Редактирование -->
            <div class="tab-pane fade <?= $id ? 'show active' : '' ?>" id="single-pane" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-file-earmark-text text-warning me-2"></i> <?= $id ? 'Редактировать параметры документа' : 'Добавить один документ' ?>
                    </h5>

                    <form method="POST" action="document_edit.php<?= $id ? '?id='.$id : '' ?>" enctype="multipart/form-data">
                        <?= getCsrfField(); ?>
                        <input type="hidden" name="upload_mode" value="single">

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Название документа:</label>
                            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($doc['title'] ?? '') ?>" placeholder="Например: Сертификат соответствия на печи Везувий">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Категория:</label>
                            <select name="category" class="form-select">
                                <?php 
                                $cats = ['Сертификаты соответствия', 'Паспорта и инструкции', 'Дилерские соглашения', 'Лицензии и допуски'];
                                $currCat = $doc['category'] ?? 'Сертификаты соответствия';
                                foreach ($cats as $c):
                                ?>
                                    <option value="<?= $c ?>" <?= $currCat === $c ? 'selected' : '' ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase">Файл документа:</label>
                            <input type="file" name="file" class="form-control" <?= $id ? '' : 'required' ?> accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
                            <div class="form-text">Поддерживаются форматы: PDF, DOC, DOCX, JPG, PNG, WEBP (до 25 МБ).</div>

                            <?php if ($id && !empty($doc['file_path'])): ?>
                                <div class="mt-2 small text-muted">
                                    Текущий файл: <a href="../<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="fw-semibold text-dark"><i class="bi bi-link-45deg"></i> Посмотреть текущий скан</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="documents.php" class="btn btn-outline-secondary rounded-pill px-4">Отмена</a>
                            <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> <?= $id ? 'Сохранить изменения' : 'Загрузить документ' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

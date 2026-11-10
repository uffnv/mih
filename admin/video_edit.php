<?php
require_once __DIR__ . '/admin_auth.php';
require_once '../includes/config.php';

// --- НАЧАЛО: ЛОГИКА ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// 1. Получение данных
$video = $id ? $pdo->prepare("SELECT * FROM videos WHERE id = ?") : null;
if ($video) {
    $video->execute([$id]);
    $video = $video->fetch();
}

// 2. Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $showOnHome = isset($_POST['show_on_home']) ? 1 : 0;
    $sortOrder = (int)$_POST['sort_order'];
    
    // Берем значение из текстового поля
    $finalVideoUrl = $_POST['video_url'];

    // 3. Обработка загрузки файла
    if (!empty($_FILES['video_file']['name'])) {
        $allowed = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
        $ext = strtolower(pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $uploadDir = '../videos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $fileName = uniqid('vid_') . '.' . $ext;
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['video_file']['tmp_name'], $uploadFile)) {
                // Если файл загружен, перезаписываем URL путем к файлу
                $finalVideoUrl = 'videos/' . $fileName;
            } else {
                $error = "Ошибка при загрузке файла.";
            }
        } else {
            $error = "Недопустимый формат видео.";
        }
    }

    if (!isset($error)) {
        $data = [
            $title,
            $finalVideoUrl, // Значение для поля video_url
            $description,
            $isActive,
            $showOnHome,
            $sortOrder
        ];

        if ($id) {
            $data[] = $id;
            // ОБРАТИТЕ ВНИМАНИЕ: поле в базе называется video_url
            $sql = "UPDATE videos SET title=?, video_url=?, description=?, is_active=?, show_on_home=?, sort_order=? WHERE id=?";
        } else {
            // ОБРАТИТЕ ВНИМАНИЕ: поле в базе называется video_url
            $sql = "INSERT INTO videos (title, video_url, description, is_active, show_on_home, sort_order) VALUES (?, ?, ?, ?, ?, ?)";
        }
        
        try {
            $pdo->prepare($sql)->execute($data);
            header("Location: videos.php");
            exit;
        } catch (PDOException $e) {
            $error = "Ошибка БД: " . $e->getMessage();
        }
    }
}
// --- КОНЕЦ ЛОГИКИ ---

require_once 'admin_header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $id ? 'Редактировать видео' : 'Добавить видео'; ?></h1>
    <a href="videos.php" class="btn btn-secondary">Назад</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Название</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($video['title'] ?? ''); ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Сортировка</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= $video['sort_order'] ?? 0; ?>">
                </div>

                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Источник видео</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Файл</label>
                                <input type="file" name="video_file" class="form-control" accept="video/*">
                            </div>
                            
                            <div class="text-center my-2 text-muted">- ИЛИ -</div>

                            <div class="mb-3">
                                <label class="form-label">Ссылка</label>
                                <!-- Здесь name="video_url" соответствует переменной $_POST['video_url'] -->
                                <input type="text" name="video_url" class="form-control" value="<?= htmlspecialchars($video['video_url'] ?? ''); ?>" placeholder="https://...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($video['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?= ($video['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="isActive">Активно</label>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="show_on_home" id="showHome" <?= ($video['show_on_home'] ?? 0) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="showHome">Показывать на главной</label>
                    </div>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

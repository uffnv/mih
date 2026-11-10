<?php
// admin/installer_edit.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// === ЛОГИКА ОБРАБОТКИ ФОРМЫ (ДО ВЫВОДА HTML) ===
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Обработка удаления фото из портфолио
if (isset($_GET['delete_portfolio'])) {
    if (!verifyCsrfToken($_GET['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }
    $pfId = (int)$_GET['delete_portfolio'];
    $stmt = $pdo->prepare("SELECT image_path FROM installer_portfolio WHERE id = ?");
    $stmt->execute([$pfId]);
    $pfImg = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($pfImg && !empty($pfImg['image_path'])) {
        $realFile = realpath(__DIR__ . '/..' . ltrim($pfImg['image_path'], '/'));
        $allowedDir = realpath(__DIR__ . '/../images/installers/portfolio');
        if ($realFile && $allowedDir && strpos($realFile, $allowedDir) === 0 && file_exists($realFile)) {
            @unlink($realFile);
        }
    }
    $pdo->prepare("DELETE FROM installer_portfolio WHERE id = ?")->execute([$pfId]);
    
    // Редирект
    header("Location: installer_edit.php?id=$id");
    exit;
}

// ОБРАБОТКА ПОСТ-ЗАПРОСА (Сохранение)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        die('Ошибка проверки CSRF токена');
    }
    // 1. Получение данных из формы
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'specialization' => $_POST['specialization'],
        'phone' => $_POST['phone'],
        'city' => $_POST['city'],
        'experience_years' => (int)$_POST['experience_years'],
        'hourly_rate' => (float)$_POST['hourly_rate'],
        'rating' => (float)$_POST['rating'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'languages' => $_POST['languages'],
        'equipment_list' => $_POST['equipment_list'],
        'insurance_info' => $_POST['insurance_info']
    ];

    // 2. Логика УЧЕТНОЙ ЗАПИСИ (User)
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Получаем текущего user_id мастера (если редактируем)
    $current_user_id = null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT user_id FROM installers WHERE id = ?");
        $stmt->execute([$id]);
        $current_user_id = $stmt->fetchColumn();
    }
    $user_id = $current_user_id;

    if (!empty($username)) {
        if ($user_id) {
            // Обновляем существующего пользователя
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $uStmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
                $uStmt->execute([$username, $hash, $user_id]);
            } else {
                $uStmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
                $uStmt->execute([$username, $user_id]);
            }
        } else {
            // Создаем нового пользователя
            // Проверка на уникальность логина
            $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $check->execute([$username]);
            if ($check->rowCount() > 0) {
                $error = "Пользователь с таким логином уже существует!";
            } else {
                $hash = isset($password) && !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : password_hash('123456', PASSWORD_DEFAULT); // Пароль по умолчанию если пустой при создании
                $email = $username . '@installer.local'; 
                $uStmt = $pdo->prepare("INSERT INTO users (username, email, password, role, first_name, last_name) VALUES (?, ?, ?, 'installer', ?, ?)");
                $uStmt->execute([$username, $email, $hash, $data['first_name'], $data['last_name']]);
                $user_id = $pdo->lastInsertId();
            }
        }
    }
    
    // Если пользователь был создан или найден, привязываем его
    if ($user_id) {
        $data['user_id'] = $user_id;
    }

    if (!isset($error)) {
        // 3. Аватар (Защищенная валидация)
        if (!empty($_FILES['photo']['name'])) {
            $up = safeUploadFile($_FILES['photo'], '../images/installers/', ['jpg', 'jpeg', 'png', 'webp'], 'inst_');
            if ($up['success']) {
                $data['photo_path'] = '/images/installers/' . $up['filename'];
            }
        } elseif ($id) {
             // Если редактируем и фото не меняли, оставляем старое (в запросе UPDATE мы не должны перезатирать путь, если он не передан, 
             // но здесь мы собираем полный массив для INSERT/UPDATE. 
             // Упростим: если фото не загружено, просто не добавляем ключ в массив $data для UPDATE, но для INSERT он нужен)
             // В данном коде мы просто не добавляем ключ, если фото нет, и база оставит старое значение при UPDATE? 
             // Нет, наш построитель запроса ниже перезапишет все поля. 
             // Поэтому нужно получить текущий путь, если он есть.
             if($id) {
                 $stmt = $pdo->prepare("SELECT photo_path FROM installers WHERE id = ?");
                 $stmt->execute([$id]);
                 $currentPhoto = $stmt->fetchColumn();
                 if($currentPhoto) $data['photo_path'] = $currentPhoto;
             }
        }

        // 4. Сохранение мастера в БД
        if ($id) {
            $fields = [];
            $params = [];
            foreach ($data as $key => $value) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
            $params[] = $id;
            $pdo->prepare("UPDATE installers SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
        } else {
            $cols = implode(', ', array_keys($data));
            $vals = implode(', ', array_fill(0, count($data), '?'));
            $pdo->prepare("INSERT INTO installers ($cols) VALUES ($vals)")->execute(array_values($data));
            $id = $pdo->lastInsertId();
        }

        // 5. Портфолио (Защищенная валидация)
        if (!empty($_FILES['portfolio']['name'][0])) {
            foreach ($_FILES['portfolio']['name'] as $key => $name) {
                if ($_FILES['portfolio']['error'][$key] === UPLOAD_ERR_OK) {
                    $itemFile = [
                        'name' => $name,
                        'tmp_name' => $_FILES['portfolio']['tmp_name'][$key],
                        'type' => $_FILES['portfolio']['type'][$key],
                        'error' => 0,
                        'size' => $_FILES['portfolio']['size'][$key]
                    ];
                    $up = safeUploadFile($itemFile, '../images/installers/portfolio/', ['jpg', 'jpeg', 'png', 'webp'], 'work_');
                    if ($up['success']) {
                        $dbPath = '/images/installers/portfolio/' . $up['filename'];
                        $stmt = $pdo->prepare("INSERT INTO installer_portfolio (installer_id, image_path) VALUES (?, ?)");
                        $stmt->execute([$id, $dbPath]);
                    }
                }
            }
        }

        // Редирект после успешного сохранения
        header("Location: installer_edit.php?id=$id&success=1");
        exit;
    }
}

// === ЗАГРУЗКА ДАННЫХ ДЛЯ ОТОБРАЖЕНИЯ (ЕСЛИ НЕ POST) ===
require_once __DIR__ . '/admin_header.php'; // Подключаем хедер ТОЛЬКО ЗДЕСЬ

$installer = [];
$portfolio = [];

// Данные по умолчанию
$formData = [
    'first_name' => '',
    'last_name' => '',
    'specialization' => '',
    'phone' => '',
    'city' => '',
    'experience_years' => 0,
    'hourly_rate' => 0.00,
    'rating' => 5.00,
    'is_active' => 1,
    'languages' => '',
    'equipment_list' => '',
    'insurance_info' => '',
    'photo_path' => '',
    'username' => '', 
    'password' => '' 
];

if ($id) {
    $stmt = $pdo->prepare("SELECT i.*, u.username FROM installers i LEFT JOIN users u ON i.user_id = u.id WHERE i.id = ?");
    $stmt->execute([$id]);
    $installer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($installer) {
        $formData = array_merge($formData, $installer);
    }

    // Портфолио
    $stmt = $pdo->prepare("SELECT * FROM installer_portfolio WHERE installer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$id]);
    $portfolio = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- HTML ФОРМА -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= $id ? 'Редактирование монтажника' : 'Новый монтажник' ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="installers.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Назад
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Данные успешно сохранены!</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="row g-4">
    <?= getCsrfField(); ?>
    <!-- Левая колонка -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header fw-bold">Личные данные</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Имя *</label>
                        <input type="text" class="form-control" name="first_name" required value="<?= htmlspecialchars($formData['first_name']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Фамилия *</label>
                        <input type="text" class="form-control" name="last_name" required value="<?= htmlspecialchars($formData['last_name']) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Специализация</label>
                    <input type="text" class="form-control" name="specialization" value="<?= htmlspecialchars($formData['specialization']) ?>">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($formData['phone']) ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Город</label>
                        <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($formData['city']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Блок Доступа -->
        <div class="card mb-4 border-warning">
            <div class="card-header fw-bold bg-warning bg-opacity-10 text-dark">
                <i class="bi bi-key"></i> Доступ в личный кабинет
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Логин (для входа)</label>
                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($formData['username'] ?? '') ?>" autocomplete="off">
                        <div class="form-text">Оставьте пустым, если доступ не нужен.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Пароль</label>
                        <input type="password" class="form-control" name="password" autocomplete="new-password">
                        <div class="form-text"><?= $id ? 'Заполните только чтобы изменить пароль.' : 'Обязательно для создания доступа.' ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header fw-bold">Портфолио</div>
            <div class="card-body">
                <input type="file" class="form-control mb-3" name="portfolio[]" multiple accept="image/*">
                <div class="row g-2">
                    <?php foreach ($portfolio as $img): ?>
                        <div class="col-6 col-md-3 position-relative">
                            <img src="<?= htmlspecialchars($img['image_path']) ?>" class="img-thumbnail w-100" style="height: 100px; object-fit: cover;">
                            <a href="?id=<?= $id ?>&delete_portfolio=<?= $img['id'] ?>&csrf_token=<?= generateCsrfToken(); ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 px-1" onclick="return confirm('Удалить фото?')">&times;</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Правая колонка -->
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header fw-bold">Статус и Рейтинг</div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" <?= $formData['is_active'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="isActive">Активен (показывать на сайте)</label>
                </div>
                <div class="mb-3">
                    <label class="form-label">Рейтинг (0-5)</label>
                    <input type="number" step="0.1" min="0" max="5" class="form-control" name="rating" value="<?= $formData['rating'] ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Ставка в час (руб)</label>
                    <input type="number" step="0.01" class="form-control" name="hourly_rate" value="<?= $formData['hourly_rate'] ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Опыт (лет)</label>
                    <input type="number" class="form-control" name="experience_years" value="<?= $formData['experience_years'] ?>">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header fw-bold">Аватар</div>
            <div class="card-body text-center">
                <?php if (!empty($formData['photo_path'])): ?>
                    <img src="<?= htmlspecialchars($formData['photo_path']) ?>" class="img-fluid rounded mb-3" style="max-height: 150px;">
                <?php endif; ?>
                <input type="file" class="form-control" name="photo" accept="image/*">
            </div>
        </div>

        <div class="card">
            <div class="card-header fw-bold">Дополнительно</div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label">Языки</label>
                    <input type="text" class="form-control form-control-sm" name="languages" value="<?= htmlspecialchars($formData['languages']) ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label">Оборудование</label>
                    <textarea class="form-control form-control-sm" name="equipment_list" rows="3"><?= htmlspecialchars($formData['equipment_list']) ?></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Страховка</label>
                    <textarea class="form-control form-control-sm" name="insurance_info" rows="2"><?= htmlspecialchars($formData['insurance_info']) ?></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 text-end pb-5">
        <button type="submit" class="btn btn-success btn-lg px-5">
            <i class="bi bi-save"></i> Сохранить монтажника
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/admin_footer.php'; ?>

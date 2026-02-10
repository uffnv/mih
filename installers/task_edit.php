<?php
require_once 'header.php';

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$installer_id = $current_installer['id'];

// Получаем задачу и проверяем, принадлежит ли она мастеру
$stmt = $pdo->prepare("SELECT * FROM installer_requests WHERE id = ? AND installer_id = ?");
$stmt->execute([$task_id, $installer_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    die('<div class="alert alert-danger">Заявка не найдена или доступ запрещен.</div></div></body></html>');
}

// Обработка смены статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_status'])) {
    $new_status = $_POST['new_status'];
    $comment = $_POST['admin_notes'] ?? ''; // Можно использовать поле заметок для комментариев мастера
    
    // Разрешенные статусы для мастера
    $allowed = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
    
    if (in_array($new_status, $allowed)) {
        // Если мастер добавляет заметку, мы можем дописывать ее к существующим
        // Но для простоты пока просто обновляем статус
        $upd = $pdo->prepare("UPDATE installer_requests SET status = ?, updated_at = NOW() WHERE id = ?");
        $upd->execute([$new_status, $task_id]);
        
        // Перезагрузка
        header("Location: task_edit.php?id=$task_id&updated=1");
        exit;
    }
}
?>

<div class="mb-3">
    <a href="index.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Назад к списку</a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-warning bg-opacity-25">
        <h4 class="mb-0">Управление заявкой #<?= $task['id'] ?></h4>
    </div>
    <div class="card-body">
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Статус заявки успешно обновлен!</div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6 border-end">
                <h5 class="fw-bold">Данные клиента</h5>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item px-0">
                        <span class="text-muted">Имя:</span> <strong><?= htmlspecialchars($task['client_name']) ?></strong>
                    </li>
                    <li class="list-group-item px-0">
                        <span class="text-muted">Телефон:</span> 
                        <a href="tel:<?= htmlspecialchars($task['client_phone']) ?>" class="fw-bold"><?= htmlspecialchars($task['client_phone']) ?></a>
                    </li>
                    <li class="list-group-item px-0">
                        <span class="text-muted">Адрес:</span> <?= htmlspecialchars($task['address']) ?>
                    </li>
                    <li class="list-group-item px-0">
                        <span class="text-muted">Желаемая дата:</span> 
                        <?= $task['preferred_date'] ? date('d.m.Y', strtotime($task['preferred_date'])) : 'Не указана' ?>
                    </li>
                </ul>
                
                <h5 class="fw-bold">Детали заказа</h5>
                <div class="p-3 bg-light rounded border">
                    <?= nl2br(htmlspecialchars($task['description'] ?? 'Нет описания')) ?>
                </div>
            </div>
            
            <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                <h5 class="fw-bold mb-3">Статус работы</h5>
                
                <form method="post" class="p-4 border rounded bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Текущий статус:</label>
                        <select name="new_status" class="form-select form-select-lg">
                            <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Новая (Ожидает)</option>
                            <option value="confirmed" <?= $task['status'] == 'confirmed' ? 'selected' : '' ?>>Подтверждена</option>
                            <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>🔨 В работе</option>
                            <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>✅ Выполнена</option>
                            <option value="cancelled" <?= $task['status'] == 'cancelled' ? 'selected' : '' ?>>❌ Отменена</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Обновить статус</button>
                </form>
                
                <div class="mt-4">
                    <h6>Памятка:</h6>
                    <ul class="small text-muted">
                        <li>Ставьте "В работе", когда выехали на объект или начали монтаж.</li>
                        <li>Ставьте "Выполнена" только после подписания акта и оплаты.</li>
                        <li>При отмене свяжитесь с менеджером.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</body>
</html>

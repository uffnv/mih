<?php
require_once 'header.php';

// Получаем заявки для текущего мастера
$installer_id = $current_installer['id'];

// Запрос заявок (предполагаем таблицу installer_requests)
// Статусы: pending (новая), in_progress (в работе), completed (выполнена), cancelled (отмена)
$stmt = $pdo->prepare("
    SELECT * FROM installer_requests 
    WHERE installer_id = ? 
    ORDER BY FIELD(status, 'in_progress', 'pending', 'completed', 'cancelled'), created_at DESC
");
$stmt->execute([$installer_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Перевод статусов
$statuses = [
    'pending' => ['text' => 'Новая', 'class' => 'bg-info'],
    'confirmed' => ['text' => 'Подтверждена', 'class' => 'bg-primary'],
    'in_progress' => ['text' => 'В работе', 'class' => 'bg-warning text-dark'],
    'completed' => ['text' => 'Выполнена', 'class' => 'bg-success'],
    'cancelled' => ['text' => 'Отменена', 'class' => 'bg-secondary']
];
?>

<h2 class="mb-4">Мои задачи и Заявки</h2>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if (!empty($tasks)): ?>
        <?php foreach ($tasks as $task): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white">
                        <span class="fw-bold">Заявка #<?= $task['id'] ?></span>
                        <?php 
                            $st = $statuses[$task['status']] ?? ['text' => $task['status'], 'class' => 'bg-secondary'];
                        ?>
                        <span class="badge <?= $st['class'] ?>"><?= $st['text'] ?></span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($task['request_type'] == 'installation' ? 'Монтаж' : ($task['request_type'] == 'repair' ? 'Ремонт' : 'Консультация')) ?></h5>
                        <p class="card-text text-muted small mb-2">
                            <i class="bi bi-calendar-event"></i> <?= date('d.m.Y', strtotime($task['created_at'])) ?>
                        </p>
                        
                        <div class="mb-3">
                            <strong>Клиент:</strong> <?= htmlspecialchars($task['client_name']) ?><br>
                            <strong>Телефон:</strong> <a href="tel:<?= htmlspecialchars($task['client_phone']) ?>"><?= htmlspecialchars($task['client_phone']) ?></a><br>
                            <strong>Адрес:</strong> <?= htmlspecialchars($task['address']) ?>
                        </div>
                        
                        <?php if (!empty($task['description'])): ?>
                            <div class="alert alert-light border p-2 small">
                                <?= nl2br(htmlspecialchars($task['description'])) ?>
                            </div>
                        <?php endif; ?>

                        <a href="task_edit.php?id=<?= $task['id'] ?>" class="btn btn-outline-dark w-100 mt-2">
                            <i class="bi bi-eye"></i> Открыть детали
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">У вас пока нет активных заявок.</div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

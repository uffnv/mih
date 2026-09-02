<?php
require_once '../includes/config.php';
require_once 'admin_header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Access denied');
}

// Фильтр по согласию ПДН
$filter_pdn = $_GET['pdn'] ?? '';
$where = '';
if ($filter_pdn === '1') $where = 'WHERE personal_data_agreed = 1';
if ($filter_pdn === '0') $where = 'WHERE personal_data_agreed = 0';

$stmt = $pdo->query("SELECT * FROM users $where ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Пользователи</h1>
        <div class="d-flex gap-2">
            <a href="users.php" class="btn btn-sm <?= $filter_pdn === '' ? 'btn-dark' : 'btn-outline-secondary' ?>">Все</a>
            <a href="users.php?pdn=1" class="btn btn-sm <?= $filter_pdn === '1' ? 'btn-success' : 'btn-outline-success' ?>">
                <i class="bi bi-shield-check"></i> Дали согласие
            </a>
            <a href="users.php?pdn=0" class="btn btn-sm <?= $filter_pdn === '0' ? 'btn-danger' : 'btn-outline-danger' ?>">
                <i class="bi bi-shield-x"></i> Без согласия
            </a>
        </div>
    </div>

    <!-- Счётчики -->
    <?php
    $total     = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $agreed    = $pdo->query("SELECT COUNT(*) FROM users WHERE personal_data_agreed = 1")->fetchColumn();
    $not_agreed = $total - $agreed;
    ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-white bg-secondary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-uppercase fw-bold opacity-75">Всего</div>
                        <div class="fs-3 fw-bold"><?= $total ?></div>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-white bg-success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-uppercase fw-bold opacity-75">Дали согласие на ПДН</div>
                        <div class="fs-3 fw-bold"><?= $agreed ?></div>
                    </div>
                    <i class="bi bi-shield-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-white bg-danger">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-uppercase fw-bold opacity-75">Без согласия на ПДН</div>
                        <div class="fs-3 fw-bold"><?= $not_agreed ?></div>
                    </div>
                    <i class="bi bi-shield-x fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th>Роль</th>
                            <th>Согласие ПДН</th>
                            <th>Дата согласия</th>
                            <th>Регистрация</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td class="text-muted small"><?= $u['id'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></div>
                                        <div class="small text-muted">@<?= htmlspecialchars($u['username']) ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
                                    <td>
                                        <?php
                                        $roleLabels = [
                                            'admin'     => ['bg-danger',  'Админ'],
                                            'moderator' => ['bg-warning text-dark', 'Модератор'],
                                            'installer' => ['bg-info text-dark',    'Мастер'],
                                            'customer'  => ['bg-secondary',         'Клиент'],
                                        ];
                                        [$cls, $lbl] = $roleLabels[$u['role']] ?? ['bg-secondary', $u['role']];
                                        ?>
                                        <span class="badge <?= $cls ?>"><?= $lbl ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($u['personal_data_agreed']): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle-fill"></i> Дано
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle-fill"></i> Не дано
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted">
                                        <?php if ($u['personal_data_agreed_at']): ?>
                                            <?= date('d.m.Y H:i', strtotime($u['personal_data_agreed_at'])) ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted">
                                        <?= date('d.m.Y', strtotime($u['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Пользователи не найдены</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

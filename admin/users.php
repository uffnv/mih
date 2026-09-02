<?php
require_once '../includes/config.php';
$page_title = 'Управление пользователями';
require_once 'admin_header.php';

// Фильтр по согласию ПДН
$filter_pdn = $_GET['pdn'] ?? '';
$where = '';
if ($filter_pdn === '1') $where = 'WHERE personal_data_agreed = 1';
if ($filter_pdn === '0') $where = 'WHERE personal_data_agreed = 0';

$stmt = $pdo->query("SELECT * FROM users $where ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$agreed = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE personal_data_agreed = 1")->fetchColumn();
$not_agreed = $total - $agreed;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Пользователи (<?= count($users) ?>)</h1>
        <small class="text-muted">Управление учетными записями клиентов и реестром согласий на ПДН (152-ФЗ)</small>
    </div>
    <div class="d-flex gap-2">
        <a href="users.php" class="btn btn-sm rounded-pill <?= $filter_pdn === '' ? 'btn-dark fw-bold' : 'btn-outline-secondary' ?>">Все (<?= $total ?>)</a>
        <a href="users.php?pdn=1" class="btn btn-sm rounded-pill <?= $filter_pdn === '1' ? 'btn-success fw-bold' : 'btn-outline-success' ?>">
            <i class="bi bi-shield-check me-1"></i> Согласие дано (<?= $agreed ?>)
        </a>
        <a href="users.php?pdn=0" class="btn btn-sm rounded-pill <?= $filter_pdn === '0' ? 'btn-danger fw-bold' : 'btn-outline-danger' ?>">
            <i class="bi bi-shield-x me-1"></i> Без согласия (<?= $not_agreed ?>)
        </a>
    </div>
</div>

<!-- Счётчики ПДН -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card admin-card bg-white h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Всего пользователей</div>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?= $total ?></h3>
                </div>
                <div class="rounded-circle bg-dark bg-opacity-10 text-dark d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-people fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card admin-card bg-white h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Согласие на ПДН получено</div>
                    <h3 class="fw-bold mb-0 text-success mt-1"><?= $agreed ?></h3>
                    <small class="text-success"><i class="bi bi-shield-check"></i> Соответствует 152-ФЗ</small>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-shield-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card admin-card bg-white h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Без фиксации согласия</div>
                    <h3 class="fw-bold mb-0 text-danger mt-1"><?= $not_agreed ?></h3>
                    <small class="text-muted">Старые учетные записи</small>
                </div>
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-shield-exclamation fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 py-3" style="width: 50px;">ID</th>
                        <th class="py-3">Пользователь</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Телефон</th>
                        <th class="py-3">Роль</th>
                        <th class="py-3">Согласие ПДН (152-ФЗ)</th>
                        <th class="py-3">Дата согласия</th>
                        <th class="py-3">Дата регистрации</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">#<?= $u['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: '—' ?></div>
                                    <small class="text-muted">@<?= htmlspecialchars($u['username']) ?></small>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($u['email']) ?>" class="text-decoration-none text-dark small">
                                        <?= htmlspecialchars($u['email']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (!empty($u['phone'])): ?>
                                        <a href="tel:<?= htmlspecialchars($u['phone']) ?>" class="text-decoration-none text-muted small">
                                            <?= htmlspecialchars($u['phone']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $roleLabels = [
                                        'admin'     => ['bg-danger',  'Администратор'],
                                        'moderator' => ['bg-warning text-dark', 'Модератор'],
                                        'installer' => ['bg-info text-dark',    'Мастер'],
                                        'customer'  => ['bg-secondary',         'Покупатель'],
                                        'user'      => ['bg-secondary',         'Пользователь'],
                                    ];
                                    [$cls, $lbl] = $roleLabels[$u['role']] ?? ['bg-secondary', $u['role']];
                                    ?>
                                    <span class="badge <?= $cls ?> rounded-pill px-2"><?= $lbl ?></span>
                                </td>
                                <td>
                                    <?php if ($u['personal_data_agreed']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                            <i class="bi bi-shield-check me-1"></i> Согласие дано
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">
                                            <i class="bi bi-shield-x me-1"></i> Не зафиксировано
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted text-nowrap">
                                    <?php if ($u['personal_data_agreed_at']): ?>
                                        <i class="bi bi-clock me-1 text-success"></i><?= date('d.m.Y H:i', strtotime($u['personal_data_agreed_at'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted text-nowrap">
                                    <?= date('d.m.Y', strtotime($u['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-people display-4 opacity-50 d-block mb-2"></i>
                                Пользователи не найдены
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

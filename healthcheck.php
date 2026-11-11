<?php
// healthcheck.php — Быстрая диагностика работоспособности окружения
header('Content-Type: text/html; charset=utf-8');

$status = [
    'php_version' => PHP_VERSION,
    'php_ok' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'mbstring' => extension_loaded('mbstring'),
    'config_local_exists' => file_exists(__DIR__ . '/includes/config.local.php'),
    'db_connected' => false,
    'db_error' => null,
    'dirs_status' => []
];

// Проверка каталогов загрузки и прав доступа под Jino JH04 / CloudLinux LVE
$checkDirs = [
    'images/products' => 'Каталог фото товаров',
    'images/categories' => 'Каталог фото категорий',
    'images/installers' => 'Каталог фото монтажников',
    'images/documents' => 'Каталог документов/сертификатов',
    'videos' => 'Каталог видеообзоров'
];

foreach ($checkDirs as $dirRel => $dirLabel) {
    $dirFull = __DIR__ . '/' . $dirRel;
    if (!is_dir($dirFull)) {
        @mkdir($dirFull, 0775, true);
    }
    $exists = is_dir($dirFull);
    $writable = $exists && is_writable($dirFull);
    $perms = $exists ? substr(sprintf('%o', fileperms($dirFull)), -4) : 'none';
    $status['dirs_status'][$dirRel] = [
        'label' => $dirLabel,
        'exists' => $exists,
        'writable' => $writable,
        'perms' => $perms
    ];
}

try {
    require_once __DIR__ . '/includes/config.php';
    if (isset($pdo) && $pdo instanceof PDO) {
        $status['db_connected'] = true;
        // Проверяем версию MySQL и базовую таблицу
        $stmt = $pdo->query("SELECT VERSION() as v");
        $status['mysql_version'] = $stmt->fetchColumn();
        
        $stmtTables = $pdo->query("SHOW TABLES LIKE 'products'");
        $status['products_table_exists'] = $stmtTables->rowCount() > 0;
    }
} catch (Throwable $e) {
    $status['db_error'] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Диагностика сервера — Территория Тепла</title>
    <style>
        body { font-family: monospace; padding: 2rem; background: #111417; color: #f8fafc; }
        .card { background: #181c20; border: 1px solid #374151; border-radius: 8px; padding: 1.5rem; max-width: 600px; margin: 0 auto; }
        h2 { color: #FFDF1E; margin-top: 0; }
        .ok { color: #10b981; font-weight: bold; }
        .err { color: #ef4444; font-weight: bold; }
        ul { list-style: none; padding: 0; }
        li { padding: 0.5rem 0; border-bottom: 1px solid #2d3748; }
        .note { font-size: 0.85rem; color: #9ca3af; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Диагностика окружения PHP / Jino</h2>
        <ul>
            <li>PHP Версия: <span class="<?= $status['php_ok'] ? 'ok' : 'err' ?>"><?= htmlspecialchars($status['php_version']) ?></span></li>
            <li>Модуль PDO MySQL: <span class="<?= $status['pdo_mysql'] ? 'ok' : 'err' ?>"><?= $status['pdo_mysql'] ? 'Включен [OK]' : 'ОТКЛЮЧЕН [ERR]' ?></span></li>
            <li>Файл includes/config.local.php: <span class="<?= $status['config_local_exists'] ? 'ok' : 'err' ?>"><?= $status['config_local_exists'] ? 'Присутствует [OK]' : 'ОТСУТСТВУЕТ (требуется создать) [ERR]' ?></span></li>
            <li>Подключение к БД: <span class="<?= $status['db_connected'] ? 'ok' : 'err' ?>"><?= $status['db_connected'] ? 'Успешно [OK]' : 'ОШИБКА [ERR]' ?></span></li>
            <?php if (!empty($status['mysql_version'])): ?>
                <li>MySQL Версия: <span class="ok"><?= htmlspecialchars($status['mysql_version']) ?></span></li>
            <?php endif; ?>
            <?php if (isset($status['products_table_exists'])): ?>
                <li>Таблица products в БД: <span class="<?= $status['products_table_exists'] ? 'ok' : 'err' ?>"><?= $status['products_table_exists'] ? 'Найдена [OK]' : 'НЕ НАЙДЕНА (импортируйте schema.sql)' ?></span></li>
            <?php endif; ?>
            <?php if (!empty($status['db_error'])): ?>
                <li class="err">Текст ошибки БД: <?= htmlspecialchars($status['db_error']) ?></li>
            <?php endif; ?>
        </ul>

        <h3 style="color: #479eb2; margin-top: 1.5rem; margin-bottom: 0.5rem;">Права каталогов загрузки (CloudLinux LVE / JH04)</h3>
        <ul>
            <?php foreach ($status['dirs_status'] as $dir => $dInfo): ?>
                <li>
                    <?= htmlspecialchars($dInfo['label']) ?> (<code><?= htmlspecialchars($dir) ?></code>):<br>
                    Права: <code><?= htmlspecialchars($dInfo['perms']) ?></code> | 
                    Запись PHP: <span class="<?= $dInfo['writable'] ? 'ok' : 'err' ?>"><?= $dInfo['writable'] ? 'РАЗРЕШЕНА [OK]' : 'ЗАПРЕЩЕНА [ERR]' ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="note">После проверки удалите этот файл (healthcheck.php) в целях безопасности.</div>
    </div>
</body>
</html>

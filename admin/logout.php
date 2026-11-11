<?php
// admin/logout.php — Безопасное завершение сессии администратора

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$reason = $_GET['reason'] ?? '';

// 1. Очищаем массив сессии
$_SESSION = [];

// 2. Удаляем куку сессии
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'] ?? '/',
        $params['domain'] ?? '',
        $params['secure'] ?? false,
        $params['httponly'] ?? true
    );
}

// 3. Уничтожаем сессию
session_destroy();

if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

$target = ($reason === 'timeout') ? '../login.php?timeout=1' : '../login.php?logged_out=1';

if (!headers_sent()) {
    header('Location: ' . $target);
} else {
    $safeTarget = htmlspecialchars($target, ENT_QUOTES, 'UTF-8');
    echo '<script>window.location.href="' . $safeTarget . '";</script>';
    echo '<meta http-equiv="refresh" content="0;url=' . $safeTarget . '">';
}
exit;

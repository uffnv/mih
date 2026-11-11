<?php
// admin/admin_auth.php — Строгая проверка авторизации и временной сессии администратора

if (!defined('ADMIN_SESSION_IDLE_LIMIT')) {
    define('ADMIN_SESSION_IDLE_LIMIT', 900); // 15 минут бездействия
}
if (!defined('ADMIN_SESSION_ABS_LIMIT')) {
    define('ADMIN_SESSION_ABS_LIMIT', 14400); // 4 часа максимум на одну сессию
}

// Запрещаем кеширование страниц админки (защита от кнопки «Назад» на общем ПК)
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
    header('X-Frame-Options: SAMEORIGIN');
}

// Сессионная кука удаляется сразу при закрытии браузера
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Полное уничтожение сессии и редирект / 401 ответ для AJAX
 */
function forceAdminSessionExit($redirectUrl = '../login.php?timeout=1') {
    $_SESSION = [];
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
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }

    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

    if ($isAjax) {
        if (!headers_sent()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode(['ok' => false, 'expired' => true, 'redirect' => $redirectUrl]);
        exit;
    }

    if (!headers_sent()) {
        header('Location: ' . $redirectUrl);
    } else {
        $safeUrl = htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8');
        echo '<script>window.location.href="' . $safeUrl . '";</script>';
        echo '<meta http-equiv="refresh" content="0;url=' . $safeUrl . '">';
    }
    exit;
}

// 1. Проверка роли
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['admin', 'moderator'], true)) {
    forceAdminSessionExit('../login.php');
}

$now = time();
$currentUaHash = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');

// 2. Привязка сессии к User-Agent браузера
if (!empty($_SESSION['admin_ua_hash'])) {
    if (!hash_equals($_SESSION['admin_ua_hash'], $currentUaHash)) {
        forceAdminSessionExit('../login.php?timeout=1');
    }
} else {
    $_SESSION['admin_ua_hash'] = $currentUaHash;
}

// 3. Проверка таймаута бездействия и максимального срока сессии
if (isset($_SESSION['admin_last_activity'])) {
    if (($now - (int)$_SESSION['admin_last_activity']) > ADMIN_SESSION_IDLE_LIMIT) {
        forceAdminSessionExit('../login.php?timeout=1');
    }
}
if (isset($_SESSION['admin_login_time'])) {
    if (($now - (int)$_SESSION['admin_login_time']) > ADMIN_SESSION_ABS_LIMIT) {
        forceAdminSessionExit('../login.php?timeout=1');
    }
} else {
    $_SESSION['admin_login_time'] = $now;
}

// 4. Обновляем отметку активности (если это не пассивная проверка)
if (!defined('ADMIN_PING_PASSIVE')) {
    $_SESSION['admin_last_activity'] = $now;
}

$admin_session_remaining = max(0, ADMIN_SESSION_IDLE_LIMIT - ($now - (int)$_SESSION['admin_last_activity']));

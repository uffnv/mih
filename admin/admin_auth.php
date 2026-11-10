<?php
// admin/admin_auth.php — Строгая проверка авторизации администратора
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'moderator')) {
    if (!headers_sent()) {
        header('Location: ../login.php');
    } else {
        echo '<script>window.location.href="../login.php";</script>';
        echo '<meta http-equiv="refresh" content="0;url=../login.php">';
    }
    exit;
}

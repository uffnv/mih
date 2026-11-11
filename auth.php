<?php
// auth.php — Совместимый маршрутизатор единого входа/регистрации без дублирования верстки
$mode = (isset($_GET['mode']) && $_GET['mode'] === 'register') || isset($_POST['register']) ? 'register' : 'login';
$redirect = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
$query = $redirect ? ('?redirect=' . urlencode($redirect)) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'register') {
        require __DIR__ . '/register.php';
    } else {
        require __DIR__ . '/login.php';
    }
    exit;
}

header('Location: ' . ($mode === 'register' ? 'register.php' : 'login.php') . $query);
exit;

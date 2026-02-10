<?php
// includes/logout.php

// Инициализируем сессию, если она еще не стартовала
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Очищаем массив сессии
$_SESSION = array();

// 2. Удаляем куку сессии, если она есть
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Уничтожаем сессию
session_destroy();

// 4. Редирект на главную страницу (или на auth.php)
// Используем абсолютный путь от корня сайта или относительный
header("Location: ../index.php"); 
exit;
?>

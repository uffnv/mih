<?php
// installers/logout.php
session_start();

// Очищаем сессию
$_SESSION = [];
session_destroy();

// Редирект на страницу входа
header("Location: ../login.php");
exit;

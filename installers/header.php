<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/config.php';

// Проверка: авторизован ли пользователь и является ли он мастером
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'installer') {
    // Если пытается зайти админ - пускаем, иначе на вход
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin')) {
        header('Location: ../login.php');
        exit;
    }
}

// Получаем ID мастера по user_id
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM installers WHERE user_id = ?");
$stmt->execute([$user_id]);
$current_installer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current_installer && $_SESSION['role'] !== 'admin') {
    die("Ошибка: Профиль монтажника не найден.");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабинет мастера</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }
        .installer-navbar { background-color: #343a40; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark installer-navbar mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-warning" href="index.php">
            <i class="bi bi-tools"></i> Кабинет Мастера
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#instNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="instNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Мои заявки</a>
                </li>
            </ul>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">
                    <i class="bi bi-person-circle"></i> 
                    <?= htmlspecialchars($current_installer['first_name'] . ' ' . $current_installer['last_name']) ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Выход</a>
            </div>
        </div>
    </div>
</nav>
<div class="container pb-5">

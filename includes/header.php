<?php
// includes/header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Считаем товары в корзине
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cart_items = getCartItems($_SESSION['user_id']);
    $cart_count = count($cart_items);
} else if (!empty($_SESSION['guest_cart'])) {
    foreach ($_SESSION['guest_cart'] as $qty) {
        $cart_count += $qty;
    }
}

// Определяем ссылку для кабинета в зависимости от роли
$cabinet_link = 'profile.php';
$cabinet_title = 'Личный кабинет';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        $cabinet_link = 'admin/index.php';
        $cabinet_title = 'Админ-панель';
    } elseif ($_SESSION['role'] === 'installer') {
        $cabinet_link = 'installers/index.php';
        $cabinet_title = 'Кабинет мастера';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Михаил Потапыч'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="../images/documents/logo.jpg">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    <!-- Ваши стили -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: #f8f9fa;">

<!-- ШАПКА САЙТА -->
<header class="sticky-top bg-dark shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-dark container">
        <!-- Логотип -->
        <a class="navbar-brand fw-bold text-uppercase d-flex align-items-center" href="index.php">
            <img src="../images/documents/logo.jpg" alt="Логотип" style="height: 40px; width: auto;" class="me-2 rounded">
            <span class="text-white">Михаил Потапыч</span>
        </a>

        <!-- Мобильное меню (бургер) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Основное меню -->
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-medium">
                <li class="nav-item"><a class="nav-link text-light" href="catalog.php">Каталог</a></li>
                
                <!-- Ссылка на акции -->
                <li class="nav-item"><a class="nav-link text-warning fw-bold" href="promotions.php">Акции %</a></li>
                
                <li class="nav-item"><a class="nav-link text-light" href="installers.php">Монтажники</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="reviews.php">Отзывы</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="about.php">О нас</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="contacts.php">Контакты</a></li>
            </ul>

            <!-- Правая часть (Корзина, Профиль) -->
            <div class="d-flex align-items-center gap-3">
                
                <!-- Корзина -->
                <a href="cart.php" class="position-relative text-light fs-5 text-decoration-none">
                    <i class="bi bi-cart3"></i>
                    <?php if ($cart_count > 0): ?>
                        <span id="cart-count-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-dark" style="font-size: 0.6rem;">
                            <?= $cart_count; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Профиль / Вход -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= $cabinet_link; ?>" class="text-light fs-5" title="<?= $cabinet_title; ?>">
                        <i class="bi bi-person-circle"></i>
                    </a>
                <?php else: ?>
                    <!-- Новые ссылки на вход и регистрацию -->
                    <a href="login.php" class="btn btn-sm btn-warning fw-bold text-dark">Войти</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

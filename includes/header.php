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

// Ссылка для личного кабинета
$cabinet_link = 'profile.php';
$cabinet_title = 'Личный кабинет';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        $cabinet_link = 'admin/index.php';
        $cabinet_title = 'Панель управления';
    } elseif ($_SESSION['role'] === 'installer') {
        $cabinet_link = 'installers/index.php';
        $cabinet_title = 'Кабинет мастера';
    }
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Территория Тепла — Отопительное оборудование на Алтае'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="images/documents/logo.jpg">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    <!-- Надежный инженерный стиль сайта (v3.0) -->
    <link rel="stylesheet" href="css/style.css?v=3.0">
    <style>
        /* Принудительная фиксация темного фона шапки независимо от кеша браузера */
        .header-top-bar {
            background-color: #111417 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
            font-size: 0.83rem;
        }
        .main-header {
            background-color: #181c20 !important;
            border-bottom: 2px solid #f59e0b !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
        }
        .navbar-brand .brand-title {
            color: #ffffff !important;
            font-weight: 800;
            letter-spacing: -0.02em;
            font-size: 1.22rem;
            line-height: 1.1;
        }
        .navbar-brand .brand-sub {
            color: #f59e0b !important;
            font-size: 0.68rem;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .main-nav-link {
            color: #e2e8f0 !important;
            font-weight: 500;
            padding: 0.55rem 0.95rem !important;
            transition: all 0.2s ease;
            border-radius: 6px;
        }
        .main-nav-link:hover {
            color: #f59e0b !important;
            background: rgba(255, 255, 255, 0.04);
        }
        .main-nav-link.active-page {
            color: #ffffff !important;
            font-weight: 700;
            background: rgba(245, 158, 11, 0.12);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- 1. ВЕРХНЯЯ ИНФОРМАЦИОННАЯ ПЛАШКА -->
<div class="header-top-bar py-1">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-geo-alt-fill text-warning me-1"></i> с. Майма, ул. Энергетиков, д. 24</span>
            <span class="d-none d-md-inline"><i class="bi bi-clock-fill text-warning me-1"></i> Пн–Сб: 09:00 – 18:00</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="tel:+79136914443" class="text-decoration-none fw-bold text-white d-inline-flex align-items-center">
                <i class="bi bi-telephone-fill text-warning me-1"></i> +7 (913) 691-44-43
            </a>
        </div>
    </div>
</div>

<!-- 2. ОСНОВНАЯ ШАПКА САЙТА -->
<header class="sticky-top main-header">
    <nav class="navbar navbar-expand-lg navbar-dark container py-2">
        <!-- Логотип -->
        <a class="navbar-brand text-uppercase d-flex align-items-center text-decoration-none" href="index.php">
            <img src="images/documents/logo.jpg" alt="Логотип" style="height: 44px; width: auto; object-fit: contain;" class="me-2 rounded shadow-sm" onerror="this.style.display='none'">
            <div>
                <span class="brand-title d-block">ТЕРРИТОРИЯ ТЕПЛА</span>
                <span class="brand-sub d-block">Отопительное оборудование</span>
            </div>
        </a>

        <!-- Мобильное меню (гамбургер) -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Меню">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Навигация -->
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link main-nav-link <?= $current_page == 'catalog.php' ? 'active-page' : '' ?>" href="catalog.php">
                        <i class="bi bi-grid-fill text-warning me-1"></i> Каталог
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link main-nav-link <?= $current_page == 'promotions.php' ? 'active-page' : '' ?>" href="promotions.php">
                        Акции
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link main-nav-link <?= $current_page == 'installers.php' ? 'active-page' : '' ?>" href="installers.php">
                        Монтажники
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link main-nav-link <?= $current_page == 'reviews.php' ? 'active-page' : '' ?>" href="reviews.php">
                        Отзывы
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link main-nav-link <?= $current_page == 'about.php' ? 'active-page' : '' ?>" href="about.php">
                        О компании
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link main-nav-link <?= $current_page == 'contacts.php' ? 'active-page' : '' ?>" href="contacts.php">
                        Контакты
                    </a>
                </li>
            </ul>

            <!-- Действия: Кабинет и Корзина -->
            <div class="d-flex align-items-center gap-2">
                <a href="<?= htmlspecialchars($cabinet_link) ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-semibold">
                    <i class="bi bi-person-circle me-1 text-warning"></i> <?= htmlspecialchars($cabinet_title) ?>
                </a>

                <a href="cart.php" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold position-relative d-inline-flex align-items-center shadow-sm" style="background-color: #f59e0b; border-color: #f59e0b; color: #181c20;">
                    <i class="bi bi-cart3 fs-6 me-1"></i>
                    <span>Корзина</span>
                    <span id="cart-count-badge" class="badge bg-dark text-warning rounded-pill ms-2" style="<?= $cart_count > 0 ? '' : 'display:none;' ?>">
                        <?= (int)$cart_count ?>
                    </span>
                </a>
            </div>
        </div>
    </nav>
</header>

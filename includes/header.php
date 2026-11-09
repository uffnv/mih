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
        $cart_count += (int)$qty;
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
$css_version = file_exists(__DIR__ . '/../css/style.css') ? filemtime(__DIR__ . '/../css/style.css') : time();
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

    <!-- Надежный инженерный стиль (v3.2 с динамическим Cache-Busting) -->
    <link rel="stylesheet" href="css/style.css?v=<?= $css_version; ?>">
    
    <!-- Critical CSS: железобетонная фиксация контраста шапки и ключевых стилей -->
    <style>
        :root {
            --bs-warning: #f59e0b;
            --bs-warning-hover: #d97706;
            --dark-main: #181c20;
            --dark-surface: #222830;
            --status-stock: #10b981;
            --status-stock-bg: #ecfdf5;
            --status-discount: #ef4444;
            --border-color: #e2e8f0;
            --bg-page: #f8fafc;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-page);
            color: #0f172a;
        }
        .header-top-bar {
            background-color: #111417 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #cbd5e1 !important;
            font-size: 0.83rem;
        }
        .main-header {
            background-color: #181c20 !important;
            border-bottom: 2px solid #f59e0b !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35) !important;
        }
        .navbar-brand .brand-title {
            color: #ffffff !important;
            font-weight: 800;
            letter-spacing: -0.02em;
            font-size: 1.25rem;
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
            color: #f1f5f9 !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.55rem 0.9rem !important;
            transition: all 0.2s ease;
            border-radius: 6px;
        }
        .main-nav-link:hover {
            color: #f59e0b !important;
            background: rgba(255, 255, 255, 0.06);
        }
        .main-nav-link.active-page {
            color: #ffffff !important;
            font-weight: 700;
            background: rgba(245, 158, 11, 0.16);
            border-bottom: 2px solid #f59e0b;
        }
        .hero-engineering {
            background: linear-gradient(135deg, #121518 0%, #1a2027 50%, #242c35 100%) !important;
            position: relative;
            border-bottom: 3px solid #f59e0b;
            overflow: hidden;
        }
        .feature-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.1);
            border-left: 3px solid #f59e0b;
        }
        .category-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.1);
            border-color: #f59e0b;
        }
        .category-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            background: #fffbeb;
            color: #d97706;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            transition: all 0.2s ease;
        }
        .category-card:hover .category-icon {
            background: #f59e0b;
            color: #181c20;
            transform: scale(1.08);
        }
        .product-card {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 28px -6px rgba(15, 23, 42, 0.12);
            border-color: #cbd5e1;
        }
        .product-img-box {
            position: relative;
            height: 220px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            overflow: hidden;
            border-bottom: 1px solid #edf2f7;
        }
        .product-img-box img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-img-box img {
            transform: scale(1.05);
        }
        .badge-stock {
            background-color: var(--status-stock-bg);
            color: var(--status-stock);
            border: 1px solid rgba(16, 185, 129, 0.25);
            font-weight: 600;
            font-size: 0.75rem;
        }
        .badge-out-of-stock {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
            font-weight: 500;
            font-size: 0.75rem;
        }
        .badge-discount {
            background-color: var(--status-discount);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.75rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- 1. ВЕРХНЯЯ ИНФОРМАЦИОННАЯ ПЛАШКА -->
<div class="header-top-bar py-1" style="background-color: #111417 !important; border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important; color: #cbd5e1 !important;">
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
<header class="sticky-top main-header" style="background-color: #181c20 !important; border-bottom: 2px solid #f59e0b !important;">
    <nav class="navbar navbar-expand-lg navbar-dark container py-2" style="background-color: #181c20 !important;">
        <!-- Логотип -->
        <a class="navbar-brand text-uppercase d-flex align-items-center text-decoration-none" href="index.php">
            <img src="images/documents/logo.jpg" alt="Логотип" style="height: 44px; width: auto; object-fit: contain;" class="me-2 rounded shadow-sm" onerror="this.style.display='none'">
            <div>
                <span class="brand-title d-block" style="color: #ffffff !important; font-weight: 800; font-size: 1.25rem;">ТЕРРИТОРИЯ ТЕПЛА</span>
                <span class="brand-sub d-block" style="color: #f59e0b !important; font-size: 0.68rem; font-weight: 600; letter-spacing: 1.2px; text-transform: uppercase;">Отопительное оборудование</span>
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
                    <a class="nav-link main-nav-link <?= ($current_page == 'installers.php' || $current_page == 'installer.php') ? 'active-page' : '' ?>" href="installers.php">
                        Монтажники
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link main-nav-link <?= $current_page == 'videos.php' ? 'active-page' : '' ?>" href="videos.php">
                        Видео
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

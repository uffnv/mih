<?php
// admin/admin_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверка прав администратора
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'moderator')) {
    header('Location: ../login.php');
    exit;
}

if (!isset($pdo)) {
    require_once __DIR__ . '/../includes/config.php';
}

$current_page = basename($_SERVER['PHP_SELF']);

// Получаем счетчики для бейджей
$pending_orders_count = 0;
$pending_reviews_count = 0;
$new_requests_count = 0;

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
    $pending_orders_count = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM reviews WHERE is_approved = 0");
    $pending_reviews_count = (int)$stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_requests WHERE status != 'processed'");
    $new_requests_count = (int)$stmt->fetchColumn();
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Панель управления'); ?> — Михаил Потапыч</title>
    
    <!-- Favicon -->
    <link rel="icon" href="../images/documents/logo.jpg">

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --admin-sidebar-bg: #14181c;
            --admin-accent: #f59e0b;
            --admin-accent-hover: #d97706;
            --admin-hover-bg: rgba(245, 158, 11, 0.12);
        }
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: #1e293b;
        }
        .sidebar {
            min-height: 100vh;
            background-color: var(--admin-sidebar-bg);
            color: #fff;
            box-shadow: 2px 0 12px rgba(0,0,0,0.12);
            transition: all 0.25s;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 9px 16px;
            font-size: 0.88rem;
            font-weight: 500;
            border-radius: 8px;
            margin: 2px 10px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease-in-out;
        }
        .sidebar .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255,255,255,.06);
            transform: translateX(3px);
        }
        .sidebar .nav-link.active {
            color: #181c20 !important;
            background-color: var(--admin-accent) !important;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
        }
        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
        }
        .sidebar-heading {
            font-size: 0.72rem;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #64748b !important;
        }
        .admin-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03), 0 4px 12px rgba(0,0,0,0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        }
        .table thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 700;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
    </style>
</head>
<body>

<!-- Мобильная верхняя панель -->
<nav class="navbar navbar-dark bg-dark d-md-none sticky-top px-3 shadow-sm">
    <a class="navbar-brand fw-bold text-warning d-flex align-items-center gap-2" href="index.php">
        <i class="bi bi-shield-lock-fill"></i> Админка
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Меню">
        <span class="navbar-toggler-icon"></span>
    </button>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Сайдбар -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
            <div class="position-sticky pt-3 pb-5">
                <div class="px-3 mb-3 d-flex align-items-center gap-2 pb-3 border-bottom border-secondary border-opacity-25">
                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill-gear fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white small">Михаил Потапыч</div>
                        <div class="text-warning small" style="font-size: 0.75rem;">
                            <?= htmlspecialchars($_SESSION['username'] ?? 'Администратор') ?>
                        </div>
                    </div>
                </div>

                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">
                            <i class="bi bi-speedometer2"></i> <span>Панель</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($current_page, ['products.php', 'product_edit.php']) ? 'active' : '' ?>" href="products.php">
                            <i class="bi bi-box-seam"></i> <span>Товары</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($current_page, ['categories.php', 'category_edit.php']) ? 'active' : '' ?>" href="categories.php">
                            <i class="bi bi-folder2"></i> <span>Категории</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($current_page, ['orders.php', 'order_details.php']) ? 'active' : '' ?>" href="orders.php">
                            <i class="bi bi-cart3"></i> 
                            <span class="d-flex justify-content-between align-items-center w-100">
                                <span>Заказы</span>
                                <?php if ($pending_orders_count > 0): ?>
                                    <span class="badge bg-danger rounded-pill"><?= $pending_orders_count ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($current_page, ['promotions.php', 'promotion_edit.php']) ? 'active' : '' ?>" href="promotions.php">
                            <i class="bi bi-megaphone"></i> <span>Акции</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'users.php' ? 'active' : '' ?>" href="users.php">
                            <i class="bi bi-people"></i> <span>Пользователи</span>
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading px-3 mt-3 mb-1 text-uppercase">
                    <span>Отзывы и Модерация</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'reviews.php' ? 'active' : '' ?>" href="reviews.php">
                            <i class="bi bi-chat-left-text"></i> 
                            <span class="d-flex justify-content-between align-items-center w-100">
                                <span>О товарах</span>
                                <?php if ($pending_reviews_count > 0): ?>
                                    <span class="badge bg-warning text-dark rounded-pill"><?= $pending_reviews_count ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'installer_reviews.php' ? 'active' : '' ?>" href="installer_reviews.php">
                            <i class="bi bi-tools"></i> <span>О мастерах</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'site_reviews.php' ? 'active' : '' ?>" href="site_reviews.php">
                            <i class="bi bi-shop"></i> <span>О магазине</span>
                        </a>
                    </li>
                </ul>

                <h6 class="sidebar-heading px-3 mt-3 mb-1 text-uppercase">
                    <span>Контент и Заявки</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($current_page, ['installers.php', 'installer_edit.php']) ? 'active' : '' ?>" href="installers.php">
                            <i class="bi bi-person-badge"></i> <span>Монтажники</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($current_page, ['videos.php', 'video_edit.php']) ? 'active' : '' ?>" href="videos.php">
                            <i class="bi bi-play-btn"></i> <span>Видео</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($current_page, ['documents.php', 'document_edit.php']) ? 'active' : '' ?>" href="documents.php">
                            <i class="bi bi-file-earmark-pdf"></i> <span>Документы</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'contact_requests.php' ? 'active' : '' ?>" href="contact_requests.php">
                            <i class="bi bi-envelope"></i> 
                            <span class="d-flex justify-content-between align-items-center w-100">
                                <span>Обращения</span>
                                <?php if ($new_requests_count > 0): ?>
                                    <span class="badge bg-info text-dark rounded-pill"><?= $new_requests_count ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </li>
                </ul>

                <div class="px-3 my-3">
                    <hr class="border-secondary border-opacity-25">
                </div>

                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="../index.php" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> <span>Перейти на сайт</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> <span>Выйти</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Основной контент страницы -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">

<?php
// admin/admin_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверка прав администратора
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'moderator')) {
    header('Location: /auth.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔥 Админка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            color: #fff;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 10px 20px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,.1);
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Сайдбар -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
            <div class="position-sticky pt-3">
                <div class="px-3 mb-4">
                    <h5 class="fw-bold">🔥 Админка</h5>
                    <div class="small text-muted">Михаил Потапыч</div>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">
                            <i class="bi bi-speedometer2"></i> Главная
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'products.php' ? 'active' : '' ?>" href="products.php">
                            <i class="bi bi-box-seam"></i> Товары
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'categories.php' ? 'active' : '' ?>" href="categories.php">
                            <i class="bi bi-folder"></i> Категории
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'orders.php' ? 'active' : '' ?>" href="orders.php">
                            <i class="bi bi-cart"></i> Заказы
                        </a>
                    </li>
                    
                    <!-- НОВЫЙ ПУНКТ: АКЦИИ -->
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page == 'promotions.php' || $current_page == 'promotion_edit.php') ? 'active' : '' ?>" href="promotions.php">
                            <i class="bi bi-megaphone"></i> Акции
                        </a>
                    </li>
                    
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                        <span>Отзывы</span>
                    </h6>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'reviews.php' ? 'active' : '' ?>" href="reviews.php">
                            <i class="bi bi-chat-left-text"></i> Отзывы (Товары)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'installer_reviews.php' ? 'active' : '' ?>" href="installer_reviews.php">
                            <i class="bi bi-tools"></i> Отзывы (Мастера)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'site_reviews.php' ? 'active' : '' ?>" href="site_reviews.php">
                            <i class="bi bi-building"></i> Отзывы (Сайт)
                        </a>
                    </li>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                        <span>Контент</span>
                    </h6>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'installers.php' ? 'active' : '' ?>" href="installers.php">
                            <i class="bi bi-people"></i> Монтажники
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'videos.php' ? 'active' : '' ?>" href="videos.php">
                            <i class="bi bi-camera-video"></i> Видео
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'documents.php' ? 'active' : '' ?>" href="documents.php">
                            <i class="bi bi-file-earmark-text"></i> Документы
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'contact_requests.php' ? 'active' : '' ?>" href="contact_requests.php">
                            <i class="bi bi-envelope"></i> Обращения
                        </a>
                    </li>
                </ul>

                <hr>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> На сайт
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Выйти
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">

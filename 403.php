<?php
http_response_code(403);
$pageTitle = "Доступ запрещен (403)";
require_once 'includes/header.php'; 
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center">
                <div class="mb-4 text-danger">
                    <i class="bi bi-shield-lock" style="font-size: 4rem;"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">Доступ запрещен</h1>
                <p class="lead text-muted mb-4">
                    У вас нет необходимых прав для просмотра этой страницы (Ошибка 403).
                </p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="/" class="btn btn-outline-dark btn-lg px-4">На главную</a>
                    <a href="contacts.php" class="btn btn-warning btn-lg px-4">Связаться с нами</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
$pageTitle = "Страница не найдена (404)";
require_once 'includes/header.php'; 
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 text-center p-5">
                <div class="card-body">
                    <h1 class="display-1 fw-bold text-muted">404</h1>
                    <h2 class="h4 mb-3">Страница не найдена</h2>
                    <p class="text-muted mb-4">
                        К сожалению, запрашиваемая вами страница не существует. Возможно, она была удалена или адрес введен неверно.
                    </p>
                    <a href="/" class="btn btn-warning btn-lg px-4 fw-bold">
                        <i class="bi bi-house-door"></i> На главную
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

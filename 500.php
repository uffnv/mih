<?php
http_response_code(500);
$pageTitle = "Ошибка сервера (500)";
require_once 'includes/header.php'; 
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center align-items-center" style="min-height: 50vh;">
        <div class="col-md-10 text-center">
            <h1 class="display-1 fw-bolder text-secondary">500</h1>
            <h2 class="mb-4">Внутренняя ошибка сервера</h2>
            <p class="text-muted fs-5 mb-5">
                На сервере произошла непредвиденная ошибка. Мы уже знаем о ней и работаем над исправлением.<br>
                Пожалуйста, зайдите позже.
            </p>
            <a href="/" class="btn btn-primary btn-lg">
                Обновить страницу
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

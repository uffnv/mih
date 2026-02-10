<?php
http_response_code(405);
$pageTitle = "Метод не разрешен (405)";
require_once 'includes/header.php'; 
?>

<div class="container py-5 my-5">
    <div class="alert alert-secondary text-center p-5" role="alert">
        <h4 class="alert-heading display-6">Метод не разрешен!</h4>
        <p class="lead mt-3">
            Метод запроса (GET/POST) не поддерживается для этого URL.
        </p>
        <hr>
        <p class="mb-0">
            <a href="/" class="alert-link">Вернуться на безопасную страницу</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

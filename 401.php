<?php
http_response_code(401);
$pageTitle = "Требуется авторизация (401)";
require_once 'includes/header.php'; 
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center border-warning mb-3">
                <div class="card-header bg-warning text-dark fw-bold">
                    Внимание
                </div>
                <div class="card-body p-5">
                    <h5 class="card-title fs-3 mb-3">Требуется вход в систему</h5>
                    <p class="card-text text-muted mb-4">
                        Для доступа к этому разделу необходимо авторизоваться.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="login.php" class="btn btn-dark">Войти</a>
                        <a href="register.php" class="btn btn-outline-dark">Регистрация</a>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    Ошибка 401
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ошибка сервера (500) — Территория Тепла</title>
    <link rel="icon" href="/images/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-dark: #181c20;
            --brand-amber: #f59e0b;
            --brand-amber-hover: #d97706;
            --text-primary: #181c20;
            --text-secondary: #64748b;
            --bg-page: #f8fafc;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            margin: 0;
        }
        .error-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            padding: 3rem 2.5rem;
            max-width: 620px;
            width: 100%;
            text-align: center;
        }
        .error-code {
            font-size: 5.5rem;
            font-weight: 800;
            line-height: 1;
            color: var(--brand-amber);
            margin-bottom: 1rem;
            letter-spacing: -2px;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--brand-dark);
        }
        .error-desc {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn-brand {
            background-color: var(--brand-amber);
            border-color: var(--brand-amber);
            color: #ffffff;
            font-weight: 600;
            padding: 0.75rem 1.75rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-brand:hover {
            background-color: var(--brand-amber-hover);
            border-color: var(--brand-amber-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .btn-outline-dark {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        .support-info {
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        .support-info a {
            color: var(--brand-dark);
            text-decoration: none;
            font-weight: 600;
        }
        .support-info a:hover {
            color: var(--brand-amber);
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">500</div>
        <h1 class="error-title">Внутренняя ошибка сервера</h1>
        <p class="error-desc">
            На сервере произошла временная ошибка обработки запроса. 
            Мы уже зафиксировали её и устраняем причину. Попробуйте обновить страницу через несколько минут.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="/" class="btn btn-brand">
                <i class="bi bi-house-door"></i> На главную
            </a>
            <button onclick="window.location.reload();" class="btn btn-outline-dark">
                <i class="bi bi-arrow-clockwise"></i> Повторить запрос
            </button>
        </div>
        <div class="support-info">
            Отдел поддержки: <a href="tel:+79136900000">+7 (913) 690-00-00</a> &bull; 
            <a href="mailto:info@territoriatepla.ru">info@territoriatepla.ru</a>
        </div>
    </div>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Загрузка документов и сертификатов компании
$document_images = [];
try {
    $stmt = $pdo->query("SELECT * FROM company_documents ORDER BY id ASC");
    $document_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $document_images = [];
}

$page_title = "О компании — Территория Тепла";
include __DIR__ . '/includes/header.php'; 
?>

<main style="background-color: var(--bg-page, #f8fafc);">
    <!-- Hero заголовок -->
    <div class="py-5 text-white" style="background: linear-gradient(135deg, #0d0f12 0%, #1c2229 100%); border-bottom: 3px solid #FFDF1E;">
        <div class="container text-center py-4">
            <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold mb-3 shadow-sm">
                <i class="bi bi-shield-check me-1"></i> Официальный производитель и поставщик
            </span>
            <h1 class="display-5 fw-bold text-uppercase mb-2 text-white">О компании <span class="text-warning">«Территория Тепла»</span></h1>
            <p class="lead text-white-50 mb-0 max-w-600 mx-auto">
                Производство, поставка и профессиональный монтаж отопительного оборудования в Республике Алтай с 1995 года
            </p>
        </div>
    </div>

    <div class="container py-5">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Главная</a></li>
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">О компании</li>
            </ol>
        </nav>
        
        <!-- Основной блок производства и истории -->
        <div class="row align-items-center mb-5 g-4 g-lg-5">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                    <span class="badge bg-warning text-dark text-uppercase mb-2 align-self-start px-3 py-1 fw-bold">
                        Наш опыт и подход
                    </span>
                    <h2 class="fw-bold mb-3 text-dark">
                        Надежность, проверенная <span class="text-warning">алтайскими зимами</span>
                    </h2>
                    <p class="lead text-muted small mb-3">Мы создаем надежное тепло и уют в ваших домах уже более 30 лет.</p>
                    <p class="text-muted small lh-lg mb-4">
                        Предприятие начиналось как семейная мастерская печников. Сегодня ООО «МП ТЕРРИТОРИЯ ТЕПЛА» — это крупный магазин-склад и производственно-монтажная база в селе Майма. Мы проектируем и комплектуем банные комплексы, твердотопливные котельные частных усадеб, турбаз и гостиниц Горного Алтая. Каждая единица оборудования проходит строгий контроль качества и соответствует нормам пожарной безопасности СНиП.
                    </p>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-dark text-white rounded-4 text-center shadow-sm">
                                <div class="display-6 fw-bold text-warning">30+</div>
                                <div class="small opacity-75">лет на рынке</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-warning text-dark rounded-4 text-center shadow-sm">
                                <div class="display-6 fw-bold">15 000+</div>
                                <div class="small fw-semibold">довольных клиентов</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Фото склада и производства -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100 p-3">
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-light mb-3">
                        <img src="images/hero-bg.jpg" class="w-100 h-100 object-fit-cover" alt="Склад Территория Тепла" onerror="this.src='images/products/no-image.png'">
                    </div>
                    <div class="p-3">
                        <h5 class="fw-bold text-dark mb-1">Собственный магазин-склад в Майме</h5>
                        <p class="text-muted small mb-0">
                            Всегда в наличии сэндвич-трубы всех диаметров из жаропрочной стали AISI 430/304, переходники, термоизоляция (базальт, минерит) и котлы длительного горения с оперативной отгрузкой в день обращения.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Юридические реквизиты организации -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 border-bottom pb-3 mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-patch-check-fill text-warning me-2"></i> Официальные реквизиты компании
                    </h4>
                    <p class="text-muted small mb-0">Все продажи и договоры поставки оформляются официально с предоставлением кассовых чеков и гарантии</p>
                </div>
                <span class="badge bg-dark text-warning px-3 py-2 rounded-pill fs-6">ООО «МП ТЕРРИТОРИЯ ТЕПЛА»</span>
            </div>
            
            <div class="row g-3 small">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <div class="text-muted mb-1">Руководитель организации:</div>
                        <div class="fw-bold text-dark">Генеральный директор</div>
                        <div>Папин Михаил Николаевич</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <div class="text-muted mb-1">Регистрационные данные:</div>
                        <div><strong>ИНН:</strong> 0411174476</div>
                        <div><strong>КПП:</strong> 041101001</div>
                        <div><strong>ОГРН:</strong> 1150411003808</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <div class="text-muted mb-1">Адрес магазина-склада:</div>
                        <div class="text-dark">649100, Республика Алтай, Майминский р-н, с. Майма, ул. Ленина, д. 1/1, Офис 4 (ул. Энергетиков, 24)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Сертификаты и лицензии -->
        <div id="documents" class="pt-2 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="h4 fw-bold text-dark mb-0">Сертификаты и документация</h3>
                    <small class="text-muted">Официальные документы качества, дипломы и дилерские сертификаты</small>
                </div>
                <span class="badge bg-dark rounded-pill px-3 py-2"><?= count($document_images) ?> документов</span>
            </div>
            
            <?php if (!empty($document_images)): ?>
                <div class="row g-3">
                    <?php foreach ($document_images as $doc): 
                        $docFile = str_replace('../', '', $doc['file_path']);
                    ?>
                        <div class="col-6 col-sm-4 col-md-3">
                            <a href="<?= htmlspecialchars($docFile); ?>"
                                class="glightbox text-decoration-none"
                                data-gallery="documents-gallery"
                                data-title="<?= htmlspecialchars($doc['title'] ?? 'Документ'); ?>">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white" style="transition: transform 0.2s ease;">
                                    <div class="ratio ratio-1x1 p-3 bg-light">
                                        <img src="<?= htmlspecialchars($docFile); ?>"
                                             class="img-fluid object-fit-contain w-100 h-100"
                                             alt="<?= htmlspecialchars($doc['title'] ?? 'Документ'); ?>"
                                             onerror="this.src='images/hero-bg.jpg'">
                                    </div>
                                    <div class="p-3 text-center border-top">
                                        <small class="text-truncate d-block fw-semibold text-dark"><?= htmlspecialchars($doc['title'] ?? 'Сертификат'); ?></small>
                                        <span class="badge bg-warning text-dark small mt-1">Сертификат</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted bg-white">
                    <i class="bi bi-file-earmark-check fs-1 mb-2 text-warning"></i>
                    <p class="mb-0">Сертификаты соответствия ГОСТ и пожарной безопасности предоставляются по запросу покупателя</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Блок магазинов и складов со ссылкой на контакты (обе точки) -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-white" style="background: linear-gradient(135deg, #0d0f12 0%, #1c2229 100%); border-left: 4px solid #FFDF1E;">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                <div>
                    <span class="badge bg-warning text-dark text-uppercase px-3 py-1 fw-bold mb-2">Наши магазины и склады</span>
                    <h3 class="h4 fw-bold text-white mb-2">Две точки продаж и выдачи оборудования на Алтае</h3>
                    <p class="text-white-50 mb-0">Магазин-склад в с. Майма (ул. Энергетиков, д. 24) и магазин в г. Горно-Алтайск (ТЦ «Кристалл», ул. Чорос-Гуркина, 9А).</p>
                </div>
                <div>
                    <a href="contacts.php" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm text-nowrap">
                        <i class="bi bi-geo-alt-fill me-1"></i> Все адреса и схемы проезда
                    </a>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

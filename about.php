<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "О компании — Михаил Потапыч";
include __DIR__ . '/includes/header.php'; 
?>

<main class="bg-light">
    <!-- Hero заголовок -->
    <div class="hero-gradient text-white py-5">
        <div class="container text-center py-3">
            <h1 class="display-5 fw-bold text-uppercase mb-2">О компании <span class="text-warning">Михаил Потапыч</span></h1>
            <p class="lead text-white-50 mb-0">Качественное отопительное оборудование и надежный монтаж с 1995 года</p>
        </div>
    </div>

    <div class="container py-5">
        
        <!-- ОСНОВНОЙ БЛОК -->
        <div class="row align-items-center mb-5 g-4 g-lg-5">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white h-100">
                    <h2 class="fw-bold mb-3 text-dark">
                        Наше <span class="text-warning">производство</span>
                    </h2>
                    <p class="lead text-muted small mb-3">Мы создаем тепло и уют в ваших домах более 30 лет.</p>
                    <p class="text-muted small lh-lg">
                        Наше предприятие начиналось как небольшая семейная мастерская. Сегодня это современный производственный комплекс 
                        с оборудованием лазерной резки и гибки металла. Мы гордимся тем, что каждая печь и котел 
                        проходят строгий многоступенчатый контроль качества перед отправкой клиенту.
                    </p>
                    <div class="row g-3 mt-2">
                        <div class="col-6">
                            <div class="p-3 bg-dark text-white rounded-4 text-center shadow-sm">
                                <div class="display-6 fw-bold text-warning">30+</div>
                                <div class="small opacity-75">лет на рынке</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-warning text-dark rounded-4 text-center shadow-sm">
                                <div class="display-6 fw-bold">15k+</div>
                                <div class="small fw-medium">довольных клиентов</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ФОТО ПРОИЗВОДСТВА -->
            <div class="col-lg-6">
                <a href="images/documents/5.jpg" 
                   class="glightbox" 
                   data-gallery="main-production"
                   data-title="Наше производство">
                    <div class="position-relative rounded-4 overflow-hidden shadow-sm card-hover bg-white" style="min-height: 340px;">
                        <img src="images/documents/5.jpg" class="img-fluid w-100 h-100 object-fit-cover rounded-4" alt="Наше производство" onerror="this.src='images/hero-bg.jpg'">
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-25 opacity-0 hover-overlay">
                            <i class="bi bi-arrows-fullscreen text-white fs-1"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- БЛОК С ДОКУМЕНТАМИ -->
        <div id="documents" class="pt-4 mt-4">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark text-uppercase">Грамоты и сертификаты</h3>
                <div class="mx-auto mt-2" style="width: 60px; height: 3px; background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);"></div>
            </div>
            
            <?php if (!empty($document_images)): ?>
                <div class="row g-3 justify-content-center">
                    <?php foreach ($document_images as $doc): ?>
                        <div class="col-6 col-sm-4 col-md-3">
                            <a href="<?= htmlspecialchars($doc['file_path']); ?>"
                               class="glightbox"
                               data-gallery="documents-slider"
                               data-title="<?= htmlspecialchars($doc['title']); ?>">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 position-relative card-hover bg-white">
                                    <div class="ratio ratio-1x1 p-2">
                                        <img src="<?= htmlspecialchars($doc['file_path']); ?>"
                                             class="img-fluid object-fit-contain w-100 h-100 rounded-3"
                                             alt="<?= htmlspecialchars($doc['title']); ?>">
                                    </div>
                                    <div class="p-2 text-center bg-light border-top">
                                        <small class="text-truncate d-block fw-medium text-dark"><?= htmlspecialchars($doc['title']); ?></small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted bg-white">
                    <i class="bi bi-images fs-1 mb-2 text-warning"></i>
                    <p class="mb-0">Сертификаты и документы обновляются</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

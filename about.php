<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "О нас";

// Загружаем документы для нижней галереи
try {
    $stmt = $pdo->prepare("
        SELECT id, title, file_path 
        FROM company_documents 
        WHERE file_path LIKE '/images/documents/%.jpg'
           OR file_path LIKE '/images/documents/%.jpeg'
           OR file_path LIKE '/images/documents/%.png'
           OR file_path LIKE '/images/documents/%.webp'
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $document_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $document_images = [];
}
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="py-5">
    <div class="container">
        
        <!-- ОСНОВНОЙ БЛОК -->
        <div class="row align-items-center mb-5 g-5">
            <div class="col-lg-6">
                <h1 class="fw-bold text-uppercase mb-4">
                    Производство <br><span class="text-warning">Михаила Потапыча</span>
                </h1>
                <p class="lead text-muted">Мы создаем тепло и уют в ваших домах с 1995 года.</p>
                <p>
                    Наше предприятие начиналось как небольшая семейная мастерская. Сегодня это полноценный завод 
                    с современным оборудованием лазерной резки и гибки металла. Мы гордимся тем, что каждая печь 
                    проходит строгий контроль качества перед отправкой клиенту.
                </p>
                <div class="row g-3 mt-3">
                    <div class="col-6">
                        <div class="p-3 bg-dark text-white rounded text-center">
                            <div class="display-5 fw-bold text-warning">30+</div>
                            <div class="small">лет на рынке</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-warning text-dark rounded text-center">
                            <div class="display-5 fw-bold">15k+</div>
                            <div class="small">довольных клиентов</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ФОТО ПРОИЗВОДСТВА (ТЕПЕРЬ КЛИКАБЕЛЬНОЕ) -->
            <div class="col-lg-6">
                <a href="images/documents/5.jpg" 
                   class="glightbox" 
                   data-gallery="main-production"
                   data-title="Наше производство">
                    <div class="position-relative rounded overflow-hidden shadow-sm gallery-item">
                        <img src="images/documents/5.jpg" class="img-fluid w-100" alt="Наше производство" onerror="this.src='images/documents/6.jpg'">
                        <!-- Иконка при наведении -->
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0 hover-overlay">
                            <i class="bi bi-arrows-fullscreen text-white fs-1"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- БЛОК С ДОКУМЕНТАМИ -->
        <div id="documents" class="pt-5 mt-5 border-top">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h2 class="text-center fw-bold mb-4">Грамоты и сертификаты</h2>
                    
                    <?php if (!empty($document_images)): ?>
                        <div class="row g-3 justify-content-center">
                            <?php foreach ($document_images as $doc): ?>
                                <div class="col-6 col-sm-4 col-md-3">
                                    <a href="<?= htmlspecialchars($doc['file_path']); ?>"
                                       class="glightbox"
                                       data-gallery="documents-slider"
                                       data-title="<?= htmlspecialchars($doc['title']); ?>">
                                        <div class="card card-body p-0 border-0 shadow-sm overflow-hidden h-100 position-relative gallery-item">
                                            <div class="ratio ratio-1x1 bg-white">
                                                <img src="<?= htmlspecialchars($doc['file_path']); ?>"
                                                     class="img-fluid object-fit-cover w-100 h-100"
                                                     alt="<?= htmlspecialchars($doc['title']); ?>">
                                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0 hover-overlay">
                                                    <i class="bi bi-zoom-in text-white fs-3"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-images fs-1 mb-2"></i>
                            <p>Раздел наполняется</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<style>
.gallery-item {
    cursor: pointer;
    transition: all 0.3s ease;
    display: block;
}
.gallery-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}
.hover-overlay {
    background: rgba(0, 0, 0, 0.4);
    transition: opacity 0.3s ease;
}
.gallery-item:hover .hover-overlay {
    opacity: 1 !important;
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>

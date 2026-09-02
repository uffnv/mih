<?php
// includes/footer.php
?>
<footer class="bg-dark text-white pt-5 pb-4 mt-auto">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- О компании -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="images/documents/logo.jpg" alt="Логотип" style="height: 36px;" class="rounded" onerror="this.style.display='none'">
                    <h5 class="text-warning fw-bold mb-0">МИХАИЛ ПОТАПЫЧ</h5>
                </div>
                <p class="text-white-50 small pe-lg-4">
                    Мы предлагаем лучшие решения для отопления и водоснабжения вашего дома. 
                    Качественное оборудование, профессиональный монтаж и официальная гарантия на все виды работ.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="https://t.me" target="_blank" class="text-white-50 fs-5 hover-warning" title="Telegram"><i class="bi bi-telegram"></i></a>
                    <a href="https://wa.me" target="_blank" class="text-white-50 fs-5 hover-warning" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <a href="https://youtube.com" target="_blank" class="text-white-50 fs-5 hover-warning" title="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <!-- Навигация -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-warning fw-bold mb-3 text-uppercase">Навигация</h6>
                <ul class="list-unstyled text-white-50 small mb-0">
                    <li class="mb-2"><a href="catalog.php" class="text-decoration-none text-white-50 hover-white">Каталог товаров</a></li>
                    <li class="mb-2"><a href="promotions.php" class="text-decoration-none text-white-50 hover-white">Акции и скидки</a></li>
                    <li class="mb-2"><a href="installers.php" class="text-decoration-none text-white-50 hover-white">Наши монтажники</a></li>
                    <li class="mb-2"><a href="reviews.php" class="text-decoration-none text-white-50 hover-white">Отзывы клиентов</a></li>
                    <li class="mb-2"><a href="about.php" class="text-decoration-none text-white-50 hover-white">О компании</a></li>
                    <li class="mb-2"><a href="contacts.php" class="text-decoration-none text-white-50 hover-white">Контакты и адрес</a></li>
                </ul>
            </div>

            <!-- Контакты и Документы -->
            <div class="col-lg-4 col-md-6">
                <h6 class="text-warning fw-bold mb-3 text-uppercase">Контакты</h6>
                <ul class="list-unstyled text-white-50 small mb-4">
                    <li class="mb-2 d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill text-warning me-2 fs-6"></i>
                        <span>г. Горно-Алтайск, ул. Ленина, 123</span>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="bi bi-telephone-fill text-warning me-2 fs-6"></i>
                        <a href="tel:+79991234567" class="text-decoration-none text-white-50 hover-white">+7 (999) 123-45-67</a>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="bi bi-envelope-fill text-warning me-2 fs-6"></i>
                        <a href="mailto:info@potapych.ru" class="text-decoration-none text-white-50 hover-white">info@potapych.ru</a>
                    </li>
                </ul>

                <div class="small">
                    <a href="docs/personal_data_agreement.pdf" target="_blank" class="text-decoration-none text-white-50 hover-warning d-inline-flex align-items-center gap-1">
                        <i class="bi bi-shield-lock text-warning"></i>
                        <span>Политика обработки персональных данных</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-top border-secondary border-opacity-25 mt-4 pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-white-50">
            <div>
                &copy; <?= date('Y'); ?> Михаил Потапыч. Все права защищены.
            </div>
            <div class="text-muted">
                Интернет-магазин отопительного оборудования
            </div>
        </div>
    </div>
</footer>

<!-- Скрипты -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof GLightbox !== 'undefined') {
        GLightbox({ selector: '.glightbox' });
    }
});
</script>
</body>
</html>

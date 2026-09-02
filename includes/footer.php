<?php
// includes/footer.php
?>
<footer class="bg-dark text-white pt-5 pb-4 mt-auto">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- О компании -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="images/documents/logo.jpg" alt="Логотип" style="height: 38px;" class="rounded shadow-sm" onerror="this.style.display='none'">
                    <div>
                        <h5 class="text-warning fw-bold mb-0">МИХАИЛ ПОТАПЫЧ</h5>
                        <small class="text-white-50" style="font-size: 0.75rem;">ООО «МП ТЕРРИТОРИЯ ТЕПЛА»</small>
                    </div>
                </div>
                <p class="text-white-50 small pe-lg-4">
                    Производство и продажа отопительного оборудования, печей и котлов. 
                    Профессиональный монтаж и официальная гарантия качества с 1995 года.
                </p>
                <div class="small text-white-50 mb-3">
                    <div><strong>ИНН:</strong> 0411174476 | <strong>ОГРН:</strong> 1150411003808</div>
                    <div><strong>Ген. директор:</strong> Папин М. Н.</div>
                </div>
                <!-- Связь и мессенджер MAX -->
                <div class="d-flex align-items-center gap-3 mt-3">
                    <a href="https://t.me" target="_blank" class="text-white-50 fs-5 hover-warning text-decoration-none" title="Telegram">
                        <i class="bi bi-telegram"></i>
                    </a>
                    <!-- Иконка MAX -->
                    <a href="#" class="hover-scale text-decoration-none" title="Мессенджер MAX">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 32 32" fill="none" class="shadow-sm rounded-3">
                            <rect width="32" height="32" rx="8" fill="#ffc107"/>
                            <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" font-size="11" font-weight="900" font-family="system-ui, -apple-system, sans-serif" fill="#111827" letter-spacing="0.5">MAX</text>
                        </svg>
                    </a>
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
                    <li class="mb-2"><a href="about.php" class="text-decoration-none text-white-50 hover-white">О компании и реквизиты</a></li>
                    <li class="mb-2"><a href="contacts.php" class="text-decoration-none text-white-50 hover-white">Контакты и магазины</a></li>
                </ul>
            </div>

            <!-- Контакты и Документы -->
            <div class="col-lg-4 col-md-6">
                <h6 class="text-warning fw-bold mb-3 text-uppercase">Контакты и адреса</h6>
                <ul class="list-unstyled text-white-50 small mb-3">
                    <li class="mb-2 d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill text-warning me-2 fs-6 mt-1"></i>
                        <div>
                            <div><strong>с. Майма:</strong> ул. Ленина, д. 1/1 (офис и склад)</div>
                            <div><strong>г. Горно-Алтайск:</strong> ул. Чорос-Гуркина, 9А (ТЦ «Кристалл»)</div>
                        </div>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="bi bi-telephone-fill text-warning me-2 fs-6"></i>
                        <a href="tel:+79136914443" class="text-decoration-none text-white-50 hover-white fw-bold text-white">+7 (913) 691-44-43</a>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="bi bi-envelope-fill text-warning me-2 fs-6"></i>
                        <a href="mailto:mpterritoriatepla@yandex.ru" class="text-decoration-none text-white-50 hover-white">mpterritoriatepla@yandex.ru</a>
                    </li>
                </ul>

                <div class="p-2 bg-black bg-opacity-25 rounded-3 border border-secondary border-opacity-25 small">
                    <a href="docs/personal_data_agreement.pdf" target="_blank" class="text-decoration-none text-white-50 hover-warning d-inline-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock-fill text-warning fs-6"></i>
                        <span>Политика конфиденциальности и 152-ФЗ</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-top border-secondary border-opacity-25 mt-4 pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-white-50">
            <div>
                &copy; <?= date('Y'); ?> ООО «МП ТЕРРИТОРИЯ ТЕПЛА». Все права защищены.
            </div>
            <div class="text-muted">
                Официальный сайт производителя отопительного оборудования
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

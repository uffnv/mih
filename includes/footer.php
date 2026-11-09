<?php
// includes/footer.php
?>
<footer class="text-white pt-5 pb-4 mt-auto" style="background-color: #111417; border-top: 3px solid var(--bs-warning, #f59e0b);">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- О компании -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="images/documents/logo.jpg" alt="Логотип" style="height: 40px;" class="rounded shadow-sm" onerror="this.style.display='none'">
                    <div>
                        <h5 class="text-warning fw-bold mb-0">ТЕРРИТОРИЯ ТЕПЛА</h5>
                        <small class="text-white-50" style="font-size: 0.75rem;">ООО «МП ТЕРРИТОРИЯ ТЕПЛА»</small>
                    </div>
                </div>
                <p class="text-white-50 small pe-lg-4 mb-3">
                    Производство и поставка отопительного оборудования, печей, котлов и сэндвич-дымоходов. Квалифицированный монтаж и официальная гарантия на все системы.
                </p>
                <div class="small text-white-50">
                    <div><strong>ИНН:</strong> 0411174476 | <strong>ОГРН:</strong> 1150411003808</div>
                    <div><strong>Юр. адрес:</strong> Республика Алтай, с. Майма, ул. Энергетиков, д. 24</div>
                </div>
            </div>

            <!-- Навигация -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-warning fw-bold mb-3 text-uppercase" style="letter-spacing: 0.8px;">Разделы сайта</h6>
                <ul class="list-unstyled text-white-50 small mb-0">
                    <li class="mb-2"><a href="catalog.php" class="text-decoration-none text-white-50 hover-white">Каталог товаров</a></li>
                    <li class="mb-2"><a href="promotions.php" class="text-decoration-none text-white-50 hover-white">Акции и скидки</a></li>
                    <li class="mb-2"><a href="installers.php" class="text-decoration-none text-white-50 hover-white">База монтажников</a></li>
                    <li class="mb-2"><a href="reviews.php" class="text-decoration-none text-white-50 hover-white">Отзывы клиентов</a></li>
                    <li class="mb-2"><a href="about.php" class="text-decoration-none text-white-50 hover-white">О компании и сертификаты</a></li>
                    <li class="mb-2"><a href="contacts.php" class="text-decoration-none text-white-50 hover-white">Контакты и склад</a></li>
                </ul>
            </div>

            <!-- Контакты и Документы -->
            <div class="col-lg-4 col-md-6">
                <h6 class="text-warning fw-bold mb-3 text-uppercase" style="letter-spacing: 0.8px;">Контакты склада</h6>
                <ul class="list-unstyled text-white-50 small mb-3">
                    <li class="mb-2 d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill text-warning me-2 fs-6 mt-1"></i>
                        <div>
                            <div><strong>Магазин-склад:</strong> с. Майма, ул. Энергетиков, д. 24</div>
                            <div class="text-white-50">Пн–Сб: 09:00 – 18:00 (Вс: выходной)</div>
                        </div>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="bi bi-telephone-fill text-warning me-2 fs-6"></i>
                        <a href="tel:+79136914443" class="text-decoration-none text-white fw-bold">+7 (913) 691-44-43</a>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="bi bi-envelope-fill text-warning me-2 fs-6"></i>
                        <a href="mailto:mpterritoriatepla@yandex.ru" class="text-decoration-none text-white-50 hover-white">mpterritoriatepla@yandex.ru</a>
                    </li>
                </ul>

                <!-- Юридические документы (152-ФЗ и ЗоЗПП) -->
                <div class="pt-2 border-top border-secondary border-opacity-25 small">
                    <a href="terms.php" class="text-white-50 text-decoration-none hover-white d-block mb-1">
                        <i class="bi bi-file-earmark-text text-warning me-1"></i> Публичная оферта и возврат 7 дней
                    </a>
                    <a href="terms.php#privacy" class="text-white-50 text-decoration-none hover-white d-block">
                        <i class="bi bi-shield-lock text-warning me-1"></i> Политика обработки персональных данных (152-ФЗ)
                    </a>
                </div>
            </div>
        </div>

        <hr class="border-secondary border-opacity-25 my-4">

        <div class="row align-items-center small text-white-50">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                &copy; <?= date('Y'); ?> ООО «МП ТЕРРИТОРИЯ ТЕПЛА». Все права защищены.
            </div>
            <div class="col-md-6 text-center text-md-end">
                <span>Продажа и монтаж отопительного оборудования в Республике Алтай</span>
            </div>
        </div>
    </div>
</footer>

<!-- 152-ФЗ Куки Баннер -->
<div id="cookie-banner" class="position-fixed bottom-0 start-0 end-0 p-3 bg-dark text-white shadow-lg border-top border-warning" style="z-index: 9999; display: none;">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
        <div class="small text-white-50">
            <i class="bi bi-info-circle-fill text-warning me-1"></i>
            Мы используем файлы cookie для корректной работы интернет-магазина и корзины покупок в соответствии с 152-ФЗ. Продолжая использовать сайт, вы даете согласие на их обработку. Подробнее — в <a href="terms.php#privacy" class="text-warning text-decoration-underline">Политике конфиденциальности</a>.
        </div>
        <button id="cookie-accept-btn" class="btn btn-warning btn-sm fw-bold rounded-pill px-4 text-nowrap">
            Понятно, согласен
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (!localStorage.getItem('cookie_consent_accepted')) {
        const banner = document.getElementById('cookie-banner');
        if (banner) banner.style.display = 'block';
    }
    const btn = document.getElementById('cookie-accept-btn');
    if (btn) {
        btn.addEventListener('click', function() {
            localStorage.setItem('cookie_consent_accepted', '1');
            const banner = document.getElementById('cookie-banner');
            if (banner) banner.style.display = 'none';
        });
    }
});
</script>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- GLightbox JS -->
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
    if (typeof GLightbox !== 'undefined') {
        const lightbox = GLightbox({ selector: '.glightbox' });
    }
</script>
</body>
</html>

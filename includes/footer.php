<?php
// includes/footer.php
?>
<footer class="text-white pt-5 pb-4 mt-auto" style="background-color: #111417; border-top: 3px solid #f59e0b;">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- О компании -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="images/documents/logo.jpg" alt="Логотип" style="height: 42px; width: auto;" class="rounded shadow-sm" onerror="this.style.display='none'">
                    <div>
                        <h5 class="text-warning fw-bold mb-0">ТЕРРИТОРИЯ ТЕПЛА</h5>
                        <small class="text-white-50" style="font-size: 0.75rem;">ООО «МП ТЕРРИТОРИЯ ТЕПЛА»</small>
                    </div>
                </div>
                <p class="text-white-50 small pe-lg-4 mb-3">
                    Производство, поставка и профессиональный монтаж отопительного оборудования, банных печей, твердотопливных котлов длительного горения и сэндвич-дымоходов в Республике Алтай. Официальная гарантия и сервис.
                </p>
                <div class="small text-white-50">
                    <div><strong>ИНН:</strong> 0411174476 | <strong>ОГРН:</strong> 1150411003808</div>
                    <div><strong>Юр. адрес:</strong> Республика Алтай, с. Майма, ул. Энергетиков, д. 24</div>
                </div>
            </div>

            <!-- Разделы сайта -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-warning fw-bold mb-3 text-uppercase" style="letter-spacing: 0.8px;">Разделы сайта</h6>
                <ul class="list-unstyled text-white-50 small mb-0">
                    <li class="mb-2"><a href="catalog.php" class="text-decoration-none text-white-50 hover-white">Каталог оборудования</a></li>
                    <li class="mb-2"><a href="promotions.php" class="text-decoration-none text-white-50 hover-white">Акции и скидки</a></li>
                    <li class="mb-2"><a href="installers.php" class="text-decoration-none text-white-50 hover-white">Проверенные мастера</a></li>
                    <li class="mb-2"><a href="videos.php" class="text-decoration-none text-white-50 hover-white">Видеообзоры печей</a></li>
                    <li class="mb-2"><a href="reviews.php" class="text-decoration-none text-white-50 hover-white">Отзывы клиентов</a></li>
                    <li class="mb-2"><a href="about.php" class="text-decoration-none text-white-50 hover-white">О компании и сертификаты</a></li>
                    <li class="mb-2"><a href="contacts.php" class="text-decoration-none text-white-50 hover-white">Контакты и склад</a></li>
                </ul>
            </div>

            <!-- Контакты склада -->
            <div class="col-lg-4 col-md-6">
                <h6 class="text-warning fw-bold mb-3 text-uppercase" style="letter-spacing: 0.8px;">Магазин-склад в Майме</h6>
                <ul class="list-unstyled text-white-50 small mb-3">
                    <li class="mb-2 d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill text-warning me-2 fs-6 mt-1"></i>
                        <div>
                            <div><strong>База:</strong> с. Майма, ул. Энергетиков, д. 24</div>
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

                <!-- 152-ФЗ и ЗоЗПП -->
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

<!-- 152-ФЗ Удобный компактный Cookie-виджет (Floating Toast Card в правом нижнем углу) -->
<div id="cookieNoticeCard" class="position-fixed p-3 p-md-4 shadow-lg text-white" 
     style="bottom: 24px; right: 24px; max-width: 380px; width: calc(100% - 48px); background: #181c20; border: 2px solid #f59e0b; border-radius: 16px; z-index: 10050; display: none; box-shadow: 0 16px 36px rgba(0,0,0,0.65) !important;">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="badge bg-warning text-dark fw-bold text-uppercase px-2 py-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
            <i class="bi bi-shield-check me-1"></i> 152-ФЗ
        </span>
        <button type="button" class="btn-close btn-close-white small p-1" onclick="acceptCookiesFast()" aria-label="Закрыть"></button>
    </div>
    <div class="d-flex gap-2 align-items-start mb-3">
        <p class="text-white-50 mb-0 small" style="font-size: 0.82rem; line-height: 1.45;">
            Мы используем cookies для корректной работы каталога и корзины. Оставаясь на сайте, вы соглашаетесь с 
            <a href="terms.php#privacy" class="text-warning text-decoration-underline fw-semibold">Политикой конфиденциальности</a>.
        </p>
    </div>
    <button type="button" onclick="acceptCookiesFast()" class="btn btn-warning w-100 fw-bold text-dark rounded-pill py-2 text-uppercase shadow-sm" style="font-size: 0.82rem; letter-spacing: 0.5px;">
        <i class="bi bi-check-lg me-1"></i> Понятно, согласен
    </button>
</div>

<script>
function acceptCookiesFast() {
    localStorage.setItem('cookie_consent_accepted', '1');
    const card = document.getElementById('cookieNoticeCard');
    if (card) {
        card.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        setTimeout(() => { card.style.display = 'none'; }, 260);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (!localStorage.getItem('cookie_consent_accepted')) {
        const card = document.getElementById('cookieNoticeCard');
        if (card) {
            setTimeout(() => {
                card.style.display = 'block';
                card.style.opacity = '0';
                card.style.transform = 'translateY(10px)';
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                requestAnimationFrame(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
            }, 500);
        }
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

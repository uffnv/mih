        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    const IDLE_LIMIT = <?= (int)(defined('ADMIN_SESSION_IDLE_LIMIT') ? ADMIN_SESSION_IDLE_LIMIT : 900) ?>;
    const CSRF_TOKEN = <?= json_encode(function_exists('generateCsrfToken') ? generateCsrfToken() : '') ?>;
    let remaining = <?= (int)($admin_session_remaining ?? 900) ?>;
    let lastServerPing = Date.now();
    let pingInFlight = false;
    let loggedOut = false;

    const timerEls = document.querySelectorAll('.admin-session-timer');

    function formatTime(sec) {
        const s = Math.max(0, sec);
        const m = Math.floor(s / 60);
        const r = s % 60;
        return String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
    }

    function updateTimerUI() {
        const formatted = formatTime(remaining);
        timerEls.forEach(el => {
            el.textContent = formatted;
            if (remaining <= 60) {
                el.classList.remove('text-warning');
                el.classList.add('text-danger', 'fw-bolder');
            } else {
                el.classList.add('text-warning');
                el.classList.remove('text-danger', 'fw-bolder');
            }
        });
    }

    function lockScreenAndExit(reason) {
        if (loggedOut) return;
        loggedOut = true;
        try { localStorage.setItem('mih_admin_logout', String(Date.now())); } catch (e) {}

        // Немедленно скрываем данные админки с экрана
        const overlay = document.createElement('div');
        overlay.style.cssText = 'position:fixed;inset:0;z-index:999999;background:rgba(17,20,23,0.97);backdrop-filter:blur(12px);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:24px;';
        overlay.innerHTML = '<div class="rounded-circle d-flex align-items-center justify-content:center mb-3" style="width:64px;height:64px;background:#FFDF1E;color:#111417;"><i class="bi bi-shield-lock-fill fs-2"></i></div>'
            + '<h4 class="fw-bold mb-2">Сессия администратора завершена</h4>'
            + '<p class="text-white-50 mb-0">Вы были автоматически отключены из-за неактивности.</p>';
        document.body.appendChild(overlay);

        window.location.replace('logout.php?reason=' + encodeURIComponent(reason || 'timeout'));
    }

    function onUserActivity() {
        if (loggedOut) return;
        remaining = IDLE_LIMIT;
        updateTimerUI();

        const now = Date.now();
        if (!pingInFlight && CSRF_TOKEN && (now - lastServerPing) >= 45000) {
            pingInFlight = true;
            lastServerPing = now;
            const fd = new FormData();
            fd.append('csrf_token', CSRF_TOKEN);
            fetch('session_ping.php', {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => {
                if (r.status === 401) {
                    lockScreenAndExit('timeout');
                    return null;
                }
                return r.json();
            })
            .then(data => {
                if (data && data.expired) lockScreenAndExit('timeout');
            })
            .catch(() => {})
            .finally(() => { pingInFlight = false; });
        }
    }

    ['mousedown', 'keydown', 'touchstart', 'scroll'].forEach(evt => {
        window.addEventListener(evt, onUserActivity, { passive: true });
    });

    // Кросс-вкладочный выход
    window.addEventListener('storage', function(e) {
        if (e.key === 'mih_admin_logout') {
            lockScreenAndExit('timeout');
        }
    });

    // Отслеживаем кнопку «Выйти» для синхронизации всех вкладок
    document.querySelectorAll('a[href*="logout.php"]').forEach(link => {
        link.addEventListener('click', function() {
            try { localStorage.setItem('mih_admin_logout', String(Date.now())); } catch (e) {}
        });
    });

    // Не пускаем просмотр закешированной страницы кнопкой «Назад»
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            window.location.reload();
        }
    });

    updateTimerUI();
    setInterval(function() {
        if (loggedOut) return;
        remaining -= 1;
        updateTimerUI();
        if (remaining <= 0) {
            lockScreenAndExit('timeout');
        }
    }, 1000);
})();
</script>
</body>
</html>
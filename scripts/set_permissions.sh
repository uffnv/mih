#!/usr/bin/env bash
# ==============================================================================
# Скрипт установки эталонных прав доступа для платформы Jino JH04 (CloudLinux LVE)
# Проект: mih (ООО «МП ТЕРРИТОРИЯ ТЕПЛА»)
# ==============================================================================

# Определение корня проекта (где лежит этот скрипт / ..)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

cd "${PROJECT_ROOT}" || exit 1

echo ">>> Настройка прав доступа для Jino JH04 (CloudLinux LVE) в: ${PROJECT_ROOT}"

# 1. Все папки сайта — строго 755 (дает права на вход веб-серверу и владельцу)
echo "1/5 Установка 755 на директории..."
find . -type d -exec chmod 755 {} +

# 2. Все обычные файлы — строго 644 (чтение для веб-сервера, запись только владельцу)
echo "2/5 Установка 644 на файлы..."
find . -type f -exec chmod 644 {} +

# 3. Изоляция конфигурации и секретов БД (152-ФЗ и защита от соседних LVE контейнеров)
echo "3/5 Установка 600 на локальный конфиг..."
if [ -f "includes/config.local.php" ]; then
    chmod 600 includes/config.local.php
fi

# 4. Каталоги загрузок (Uploads)
# В CloudLinux LVE под mod_lsapi/FastCGI права 755 дают владельцу (PHP) полный доступ на запись.
# 775 выставляется для гарантии, если группа веб-сервера отличается.
echo "4/5 Настройка каталогов загрузки (images, videos)..."
mkdir -p images/products images/categories images/installers/portfolio images/documents images/portfolio images/promotions videos
chmod -R 775 images/products images/categories images/installers images/documents images/portfolio images/promotions videos

# 5. Исполняемые скрипты консоли (если есть)
if [ -f "scripts/set_permissions.sh" ]; then
    chmod 755 scripts/set_permissions.sh
fi

echo ">>> [ГОТОВО] Права доступа успешно согласованы с архитектурой CloudLinux LVE."

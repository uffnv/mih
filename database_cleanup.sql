-- ========================================================
-- Очистка неиспользуемых и устаревших таблиц БД
-- Проект: «Территория Тепла» (territoriatepla.ru / mih)
-- ========================================================

-- 1. Удаление дубликата категорий от старой миграции
DROP TABLE IF EXISTS `categories_new`;

-- 2. Удаление брошенного дубликата документов (актуальная таблица: company_documents)
DROP TABLE IF EXISTS `documents`;

-- 3. Удаление неиспользуемой бонусной системы (баллы)
DROP TABLE IF EXISTS `loyalty_transactions`;

-- 4. Удаление дубликата заявок на монтаж (актуальная таблица: installer_requests)
DROP TABLE IF EXISTS `installation_requests`;

-- 5. Удаление старой сводной таблицы отзывов (актуальные: product_reviews и site_reviews)
DROP TABLE IF EXISTS `reviews`;

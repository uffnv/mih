-- ==============================================================================
-- МИГРАЦИЯ БАЗЫ ДАННЫХ: v1.0 -> Current (Территория Тепла / mih)
-- Совместимо с MySQL 8+ и MariaDB 10.11 (Jino JH04 / CloudLinux)
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Исправление таблицы contact_requests (устранение ошибки id = 0 и добавление заметок)
-- Перенумеруем нули, если они накопились
SET @r := 0;
UPDATE `contact_requests` SET `id` = (@r := @r + 1) WHERE `id` = 0 OR `id` IS NULL;

-- Добавление PRIMARY KEY и AUTO_INCREMENT
ALTER TABLE `contact_requests` 
  MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT,
  ADD PRIMARY KEY (`id`);

-- Добавление admin_notes
ALTER TABLE `contact_requests`
  ADD COLUMN IF NOT EXISTS `admin_notes` TEXT NULL COMMENT 'Служебные заметки менеджера';

-- 2. Исправление таблицы product_reviews (добавление модерации отзывов)
ALTER TABLE `product_reviews`
  ADD COLUMN IF NOT EXISTS `is_approved` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = одобрен, 0 = на модерации';

-- 3. Расширение таблицы installer_applications (мультиканальная связь и город)
ALTER TABLE `installer_applications`
  ADD COLUMN IF NOT EXISTS `city` VARCHAR(100) NULL DEFAULT 'Республика Алтай' COMMENT 'Город или район мастера',
  ADD COLUMN IF NOT EXISTS `telegram` VARCHAR(100) NULL COMMENT 'Никнейм или ссылка на Telegram',
  ADD COLUMN IF NOT EXISTS `admin_notes` TEXT NULL COMMENT 'Заметки по результатам проверки анкеты';

-- 4. Расширение таблицы company_documents (категории и размер файлов)
ALTER TABLE `company_documents`
  ADD COLUMN IF NOT EXISTS `category` VARCHAR(100) NOT NULL DEFAULT 'Сертификаты соответствия' COMMENT 'Категория документа',
  ADD COLUMN IF NOT EXISTS `file_size` INT(11) NULL DEFAULT 0 COMMENT 'Размер файла в байтах';

-- 5. Защита персональных данных в таблице users (152-ФЗ)
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `personal_data_agreed` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Согласие на обработку ПДн',
  ADD COLUMN IF NOT EXISTS `personal_data_agreed_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Время согласия';

SET FOREIGN_KEY_CHECKS = 1;
-- ==============================================================================
-- МИГРАЦИЯ УСПЕШНО ЗАВЕРШЕНА
-- ==============================================================================

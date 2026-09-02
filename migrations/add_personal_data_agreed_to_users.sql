-- Миграция: добавление полей согласия на обработку персональных данных
-- Дата: 2026-09-02
-- Таблица: users

ALTER TABLE `users`
    ADD COLUMN `personal_data_agreed` tinyint(1) NOT NULL DEFAULT 0
        COMMENT 'Согласие на обработку персональных данных (152-ФЗ)' AFTER `loyalty_points`,
    ADD COLUMN `personal_data_agreed_at` timestamp NULL DEFAULT NULL
        COMMENT 'Дата и время выдачи согласия' AFTER `personal_data_agreed`;

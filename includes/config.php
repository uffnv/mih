<?php
// includes/config.php

// 1. Загрузка конфигурации из локального файла (если существует)
$localConfigFile = __DIR__ . '/config.local.php';
$localConfig = file_exists($localConfigFile) ? require $localConfigFile : [];

// 2. Параметры подключения: локальный файл -> переменные окружения -> значения по умолчанию
$host    = $localConfig['host']    ?? getenv('DB_HOST') ?: 'localhost';
$db      = $localConfig['db']      ?? getenv('DB_NAME') ?: 'heating_equipment_store';
$user    = $localConfig['user']    ?? getenv('DB_USER') ?: 'root';
$pass    = $localConfig['pass']    ?? getenv('DB_PASS') ?: '';
$charset = $localConfig['charset'] ?? 'utf8mb4';
$debug   = $localConfig['debug']   ?? false;

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Безопасность: не раскрываем путь к файлам, порт и логин БД пользователю
    if ($debug) {
        die("Ошибка подключения к БД: " . htmlspecialchars($e->getMessage()));
    } else {
        error_log("Database connection error: " . $e->getMessage());
        http_response_code(500);
        die("Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.");
    }
}
?>
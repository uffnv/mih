<?php
/**
 * Скрипт безопасного обезличивания дампа БД перед выгрузкой или передачей разработчикам (152-ФЗ)
 * Использование: php anonymize_dump.php input.sql output_clean.sql
 */

if ($argc < 3) {
    echo "Использование: php anonymize_dump.php <входной_файл.sql> <выходной_файл.sql>\n";
    exit(1);
}

$inputFile = $argv[1];
$outputFile = $argv[2];

if (!file_exists($inputFile)) {
    die("Ошибка: файл $inputFile не найден.\n");
}

$handle = fopen($inputFile, 'r');
$outHandle = fopen($outputFile, 'w');

$userIndex = 1;
while (($line = fgets($handle)) !== false) {
    // Маскируем вставки в таблицу пользователей
    if (strpos($line, "INSERT INTO `users`") !== false || strpos($line, "INSERT INTO users") !== false) {
        // Оставляем только структуру или заменяем на анонимные данные
        $line = "-- Таблица users обезличена для целей разработки (152-ФЗ)\n";
    }
    
    // Маскируем телефоны вида +7 (xxx) xxx-xx-xx
    $line = preg_replace('/\+7\s*\(\d{3}\)\s*\d{3}-\d{2}-\d{2}/', '+7 (999) 000-00-00', $line);
    
    // Маскируем email адреса
    $line = preg_replace('/[a-zA-Z0-9._%+-]+@(?!example\.com|test\.local)[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', 'user' . ($userIndex++) . '@test.local', $line);
    
    fwrite($outHandle, $line);
}

fclose($handle);
fclose($outHandle);

echo "Готово! Обезличенный дамп сохранен в $outputFile\n";

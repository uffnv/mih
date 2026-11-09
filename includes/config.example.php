<?php
// includes/config.example.php
// Пример файла локальной конфигурации для разработки и продакшена.
// Скопируйте этот файл в 'config.local.php' и укажите ваши реальные параметры БД.

return [
    'host'    => 'localhost',
    'db'      => 'heating_equipment_store',
    'user'    => 'root',
    'pass'    => '',
    'charset' => 'utf8mb4',
    'debug'   => false // true для вывода ошибок (только локально)
];

<?php
// includes/functions.php
require_once 'config.php';

/**
 * Получение количества новых заявок обратной связи
 */
function getNewContactRequestsCount() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_requests WHERE status = 'new'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        error_log("Ошибка получения количества заявок: " . $e->getMessage());
        return 0;
    }
}

/**
 * Получение всех заявок обратной связи
 */
function getContactRequests($status = null, $limit = null) {
    global $pdo;
    try {
        $sql = "SELECT * FROM contact_requests";
        
        if ($status) {
            $sql .= " WHERE status = ?";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
        }
        
        $stmt = $pdo->prepare($sql);
        
        if ($status && $limit) {
            $stmt->execute([$status, (int)$limit]);
        } elseif ($status) {
            $stmt->execute([$status]);
        } elseif ($limit) {
            $stmt->execute([(int)$limit]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения заявок: " . $e->getMessage());
        return [];
    }
}


/**
 * Обновление количества товара в корзине
 */
function updateCartQuantity($user_id, $product_id, $quantity) {
    global $pdo;
    
    try {
        if ($quantity <= 0) {
            // Удаляем товар из корзины
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            return $stmt->execute([$user_id, $product_id]);
        } else {
            // Обновляем количество
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            return $stmt->execute([$quantity, $user_id, $product_id]);
        }
    } catch (PDOException $e) {
        error_log("Ошибка обновления корзины: " . $e->getMessage());
        return false;
    }
}
/**
* Создание заказа
*/
function createOrder($user_id, $order_data) {
    global $pdo;
    try {
        $pdo->beginTransaction();

        // Расчет суммы
        $total = calculateOrderTotal($order_data['cart_items'], $order_data['delivery_method']);

        // Создаем заказ с total_price
        $stmt = $pdo->prepare("
            INSERT INTO orders
            (user_id, customer_name, customer_phone, customer_email, delivery_address, delivery_method, payment_method, comment, total_price, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");

        $stmt->execute([
            $user_id,
            $order_data['name'],
            $order_data['phone'],
            $order_data['email'],
            $order_data['address'],
            $order_data['delivery_method'],
            $order_data['payment_method'],
            $order_data['comment'],
            $total
        ]);

        $order_id = $pdo->lastInsertId();

        // Добавляем товары в заказ
        foreach ($order_data['cart_items'] as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items
                (order_id, product_id, product_name, price, quantity)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['name'],
                $item['price'],
                $item['quantity']
            ]);
        }

        $pdo->commit();
        return $order_id;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Ошибка создания заказа: " . $e->getMessage());
        return false;
    }
}
/**
 * Расчет общей суммы заказа
 */
function calculateOrderTotal($cart_items, $delivery_method) {
    $total = 0;
    
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // Добавляем стоимость доставки
    if ($delivery_method === 'courier') {
        $total += 500; // Стоимость курьерской доставки
    }
    
    return $total;
}


function clearCart($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        return $stmt->execute([$user_id]);
    } catch (PDOException $e) {
        error_log("Ошибка очистки корзины: " . $e->getMessage());
        return false;
    }
}

/**
 * РЕГИСТРАЦИЯ (С учетом полей first_name и last_name)
 */
function registerUser($username, $email, $password, $first_name, $last_name, $phone, $role = 'customer', $personal_data_agreed = false) {
    global $pdo;

    try {
        // 1. Проверка: email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'Этот Email уже зарегистрирован'];
        }

        // 2. Проверка: username
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'Этот логин уже занят'];
        }

        // 3. Хеш пароля
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 4. Вставка с фиксацией согласия на обработку персональных данных (152-ФЗ)
        $agreed_flag = $personal_data_agreed ? 1 : 0;
        $agreed_at   = $personal_data_agreed ? date('Y-m-d H:i:s') : null;

        $sql = "INSERT INTO users (username, email, password, first_name, last_name, phone, role, personal_data_agreed, personal_data_agreed_at)
                VALUES (:username, :email, :password, :fname, :lname, :phone, :role, :agreed, :agreed_at)";

        $stmt = $pdo->prepare($sql);

        $params = [
            ':username'   => $username,
            ':email'      => $email,
            ':password'   => $hashed_password,
            ':fname'      => $first_name,
            ':lname'      => $last_name,
            ':phone'      => $phone,
            ':role'       => $role,
            ':agreed'     => $agreed_flag,
            ':agreed_at'  => $agreed_at,
        ];

        if ($stmt->execute($params)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Не удалось сохранить данные'];
        }

    } catch (PDOException $e) {
        // Если ошибка "Unknown column 'first_name'", значит в базе все-таки 'firstname'
        // Смотрите лог ошибок PHP
        error_log("Registration SQL Error: " . $e->getMessage());
        
        // Возвращаем понятную ошибку
        if (strpos($e->getMessage(), 'first_name') !== false) {
             return ['success' => false, 'error' => 'Ошибка структуры БД: не найдено поле first_name'];
        }
        return ['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()];
    }
}

// Обновленная функция входа
function loginUser($email, $password) {
    global $pdo;
    // Явно выбираем id, username, password и role
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user; // Возвращаем массив с role
    }
    return false;
}

/**
 * Аутентификация пользователя
 */
function authenticateUser($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Для отладки - выводим информацию о пользователе
            error_log("Найден пользователь: " . $user['email']);
            error_log("Хеш пароля в БД: " . $user['password']);
            error_log("Введенный пароль: " . $password);
            
            if (password_verify($password, $user['password'])) {
                error_log("Пароль верный");
                return $user;
            } else {
                error_log("Пароль неверный");
            }
        } else {
            error_log("Пользователь не найден: " . $email);
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Ошибка аутентификации: " . $e->getMessage());
        return false;
    }
}

/**
 * Проверка существования email
 */
function emailExists($email) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        error_log("Ошибка проверки email: " . $e->getMessage());
        return false;
    }
}

/**
 * Генерация имени пользователя
 */
function generateUsername($first_name, $last_name) {
    $base_username = translit($first_name) . '.' . translit($last_name);
    $username = $base_username;
    $counter = 1;
    
    // Проверяем уникальность имени пользователя
    while (usernameExists($username)) {
        $username = $base_username . $counter;
        $counter++;
    }
    
    return $username;
}

/**
 * Проверка существования имени пользователя
 */
function usernameExists($username) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        error_log("Ошибка проверки username: " . $e->getMessage());
        return false;
    }
}

/**
 * Транслитерация для генерации username
 */
function translit($string) {
    $converter = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch',
        'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya'
    );
    
    $string = strtr($string, $converter);
    $string = preg_replace('/[^a-zA-Z0-9._]/', '', $string);
    $string = strtolower($string);
    
    return $string;
}

/**
 * Получение общего количества товаров
 */
function getTotalProducts() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        error_log("Ошибка получения количества товаров: " . $e->getMessage());
        return 0;
    }
}

/**
 * Получение общего количества заказов
 */
function getTotalOrders() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        error_log("Ошибка получения количества заказов: " . $e->getMessage());
        return 0;
    }
}

/**
 * Получение количества ожидающих заказов
 */
function getPendingOrdersCount() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        error_log("Ошибка получения количества ожидающих заказов: " . $e->getMessage());
        return 0;
    }
}

/**
 * Получение общего количества отзывов
 */
function getTotalReviews() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM reviews");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        error_log("Ошибка получения количества отзывов: " . $e->getMessage());
        return 0;
    }
}

/**
 * Получение общего количества монтажников
 */
function getTotalInstallers() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM installers WHERE is_active = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (PDOException $e) {
        error_log("Ошибка получения количества монтажников: " . $e->getMessage());
        return 0;
    }
}

/**
 * Получение последних заказов
 */
function getRecentOrders($limit = 5) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения последних заказов: " . $e->getMessage());
        return [];
    }
}

/**
 * Получение последних заявок обратной связи
 */
function getRecentContactRequests($limit = 5) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_requests ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения последних заявок: " . $e->getMessage());
        return [];
    }
}
/**
 * Получение всех заказов
 */
function getAllOrders() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения заказов: " . $e->getMessage());
        return [];
    }
}

/**
 * Получение заказов по статусу
 */
function getOrdersByStatus($status) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения заказов по статусу: " . $e->getMessage());
        return [];
    }
}

/**
 * Получение заказа по ID
 */
function getOrderById($order_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения заказа: " . $e->getMessage());
        return false;
    }
}

/**
 * Получение товаров заказа
 */
function getOrderItems($order_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения товаров заказа: " . $e->getMessage());
        return [];
    }
}


/**
 * Получение всех отзывов
 */
function getAllReviews() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT r.*, u.first_name, u.last_name 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения отзывов: " . $e->getMessage());
        return [];
    }
}

/**
 * Одобрение отзыва
 */
function approveReview($review_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?");
        return $stmt->execute([$review_id]);
    } catch (PDOException $e) {
        error_log("Ошибка одобрения отзыва: " . $e->getMessage());
        return false;
    }
}

/**
 * Отклонение отзыва
 */
function rejectReview($review_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE reviews SET is_approved = 0 WHERE id = ?");
        return $stmt->execute([$review_id]);
    } catch (PDOException $e) {
        error_log("Ошибка отклонения отзыва: " . $e->getMessage());
        return false;
    }
}

/**
 * Удаление отзыва
 */
function deleteReview($review_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        return $stmt->execute([$review_id]);
    } catch (PDOException $e) {
        error_log("Ошибка удаления отзыва: " . $e->getMessage());
        return false;
    }
}



/**
 * Обновление статуса заявки
 */
function updateContactRequestStatus($request_id, $status) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE contact_requests SET status = ?, processed_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$status, $request_id]);
    } catch (PDOException $e) {
        error_log("Ошибка обновления статуса заявки: " . $e->getMessage());
        return false;
    }
}

/**
 * Удаление заявки
 */
function deleteContactRequest($request_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_requests WHERE id = ?");
        return $stmt->execute([$request_id]);
    } catch (PDOException $e) {
        error_log("Ошибка удаления заявки: " . $e->getMessage());
        return false;
    }
}

// functions.php (добавлены/аккуратно оформлены функции работы с видео в конце файла)

/**
 * Автоматическая конвертация видео в MP4 (H.264 + AAC) для веба
 */
function convertVideoForWeb($inputPath, $outputPath)
{
    // Проверяем, доступен ли ffmpeg
    $ffmpegPaths = [
        '/usr/bin/ffmpeg',
        '/usr/local/bin/ffmpeg',
        'ffmpeg' // на Windows или если ffmpeg прописан в PATH
    ];

    $ffmpeg = null;
    foreach ($ffmpegPaths as $path) {
        $which = escapeshellcmd($path);
        @exec("$which -version 2>&1", $o, $c);
        if ($c === 0) {
            $ffmpeg = $path;
            break;
        }
    }

    if ($ffmpeg === null) {
        error_log('FFmpeg не доступен для конвертации видео');
        return false;
    }

    $outputDir = dirname($outputPath);
    if (!is_dir($outputDir)) {
        if (!mkdir($outputDir, 0755, true) && !is_dir($outputDir)) {
            error_log('Не удалось создать директорию для видео: ' . $outputDir);
            return false;
        }
    }

    // Оптимальные настройки для веб: H.264 + AAC, faststart для прогрессивной загрузки
    $command = sprintf(
        '%s -y -i %s -c:v libx264 -preset medium -crf 23 -c:a aac -b:a 128k -movflags +faststart -vf "scale=trunc(iw/2)*2:trunc(ih/2)*2" %s 2>&1',
        escapeshellcmd($ffmpeg),
        escapeshellarg($inputPath),
        escapeshellarg($outputPath)
    );

    $output   = [];
    $exitCode = 0;
    exec($command, $output, $exitCode);

    if ($exitCode !== 0) {
        error_log('Ошибка конвертации видео: ' . implode("\n", $output));
        return false;
    }

    return file_exists($outputPath);
}

/**
 * Загрузка и обработка видео: сохранение, конвертация в mp4, запись в БД
 */
function uploadAndProcessVideo($file, $title, $description)
{
    global $pdo;

    $uploadDir    = __DIR__ . '/../videos/';
    $publicPrefix = 'videos/';

    $allowedTypes = [
        'video/mp4',
        'video/avi',
        'video/mov',
        'video/mkv',
        'video/webm'
    ];

    if (!in_array($file['type'], $allowedTypes, true)) {
        return ['success' => false, 'message' => 'Неподдерживаемый формат видео'];
    }

    if ($file['size'] > 100 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Файл слишком большой (максимум 100MB)'];
    }

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            return ['success' => false, 'message' => 'Не удалось создать директорию для видео'];
        }
    }

    $fileExtension    = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safeName         = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $file['name']);
    $originalFileName = uniqid('video_', true) . '_' . $safeName;
    $convertedName    = pathinfo($originalFileName, PATHINFO_FILENAME) . '.mp4';

    $originalPath  = $uploadDir . 'original_' . $originalFileName;
    $convertedPath = $uploadDir . $convertedName;

    if (!move_uploaded_file($file['tmp_name'], $originalPath)) {
        return ['success' => false, 'message' => 'Ошибка загрузки файла'];
    }

    // Конвертация
    if (!convertVideoForWeb($originalPath, $convertedPath)) {
        // Если вход уже mp4 — используем его как есть
        if ($fileExtension === 'mp4') {
            $convertedPath = $originalPath;
        } else {
            @unlink($originalPath);
            return ['success' => false, 'message' => 'Ошибка конвертации видео'];
        }
    } else {
        // Оригинал больше не нужен
        @unlink($originalPath);
    }

    $relativeFileName = basename($convertedPath);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO videos (title, file_name, description, is_active, sort_order)
            VALUES (?, ?, ?, 1, 0)
        ");
        $ok = $stmt->execute([
            $title,
            $relativeFileName,
            $description
        ]);

        if ($ok) {
            return [
                'success'  => true,
                'message'  => 'Видео успешно загружено',
                'video_id' => $pdo->lastInsertId()
            ];
        }

        @unlink($convertedPath);
        return ['success' => false, 'message' => 'Ошибка сохранения в базу данных'];

    } catch (PDOException $e) {
        @unlink($convertedPath);
        error_log('Ошибка сохранения видео: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Ошибка базы данных'];
    }
}

/**
 * Проверка/создание MP4-версии видео и возврат корректного имени файла
 */
function ensureVideoFormats($videoFileName)
{
    $videosDir = __DIR__ . '/../videos/';
    $path      = $videosDir . $videoFileName;

    $ext      = strtolower(pathinfo($videoFileName, PATHINFO_EXTENSION));
    $baseName = pathinfo($videoFileName, PATHINFO_FILENAME);

    // Если уже mp4 и файл существует — просто возвращаем
    if ($ext === 'mp4' && file_exists($path)) {
        return $videoFileName;
    }

    // Путь к ожидаемому mp4
    $mp4Name = $baseName . '.mp4';
    $mp4Path = $videosDir . $mp4Name;

    // Если mp4 уже существует — используем его
    if (file_exists($mp4Path)) {
        return $mp4Name;
    }

    // Если исходный файл существует, пробуем сконвертировать
    if (file_exists($path)) {
        if (convertVideoForWeb($path, $mp4Path)) {
            return $mp4Name;
        }
    }

    // Ничего не получилось – возвращаем исходное имя (на случай, если его отдают как есть)
    return $videoFileName;
}

/**
 * Получение списка видео
 */
function get_videos($limit = null)
{
    global $pdo;

    try {
        $sql = "SELECT * FROM videos WHERE is_active = 1 ORDER BY sort_order, created_at DESC";
        if ($limit) {
            $sql .= " LIMIT ?";
        }

        $stmt = $pdo->prepare($sql);

        if ($limit) {
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log('Ошибка получения видео: ' . $e->getMessage());
        return [];
    }
}
/* === ФУНКЦИИ ДЛЯ АДМИНКИ === */


// Функция обновления статуса заказа
function updateOrderStatus($order_id, $status) {
    global $pdo;
    
    // Список допустимых статусов для безопасности
    $allowed_statuses = ['pending', 'confirmed', 'processing', 'completed', 'cancelled'];
    
    if (!in_array($status, $allowed_statuses)) {
        return false;
    }

    $sql = "UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$status, $order_id]);
}


// Получение общего числа заказов пользователя
function getTotalUserOrders($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Ошибка getTotalUserOrders: " . $e->getMessage());
        return 0;
    }
}

// Получение общего числа отзывов пользователя
function getTotalUserReviews($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Ошибка getTotalUserReviews: " . $e->getMessage());
        return 0;
    }
}

// Получение количества заказов в обработке
function getPendingUserOrders($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status IN ('pending', 'processing', 'confirmed')");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Ошибка getPendingUserOrders: " . $e->getMessage());
        return 0;
    }
}

// Получение последних заказов (для истории)
function getRecentUserOrders($user_id, $limit = 5) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Ошибка getRecentUserOrders: " . $e->getMessage());
        return [];
    }
}

// Получение активных заказов
function getActiveUserOrders($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? AND status IN ('pending', 'processing', 'confirmed') ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Ошибка getActiveUserOrders: " . $e->getMessage());
        return [];
    }
}


/**
 * Получить список товаров для выбора в отзыве
 */
function getProductsForReview() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT id, name FROM products ORDER BY name ASC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Получить список монтажников для выбора в отзыве
 */
function getInstallersForReview() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT id, name FROM installers ORDER BY name ASC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function plural($n, $form1, $form2, $form5) {
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $form5;
    if ($n1 > 1 && $n1 < 5) return $form2;
    if ($n1 == 1) return $form1;
    return $form5;
}

// includes/functions.php

// --- ФУНКЦИИ ДЛЯ ОТЗЫВОВ ---

/**
 * Получение списка товаров
 */
function getProductsList() {
    global $pdo;
    try {
        // Проверьте, есть ли колонка is_active, если нет — уберите WHERE
        $stmt = $pdo->query("SELECT id, name FROM products ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Получение списка монтажников (исправлено: first_name, last_name)
 */
function getInstallersList() {
    global $pdo;
    try {
        $sql = "SELECT id, first_name, last_name FROM installers ORDER BY last_name ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Добавление отзыва
 * Используем is_approved = 0 (на модерации)
 */
function addReview($data) {
    global $pdo;
    try {
        $sql = "INSERT INTO reviews 
                (user_id, product_id, installer_id, rating, title, comment, is_approved, created_at) 
                VALUES (:uid, :pid, :iid, :rating, :title, :comment, 0, NOW())";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':uid'    => $data['user_id'],
            ':pid'    => $data['product_id'] ?? null,
            ':iid'    => $data['installer_id'] ?? null,
            ':rating' => $data['rating'],
            ':title'  => $data['title'],
            ':comment'=> $data['comment']
        ]);
    } catch (PDOException $e) {
        error_log("addReview Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Средний рейтинг (только одобренные)
 */
function getAverageRating() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE is_approved = 1");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'average' => round((float)($res['avg'] ?? 0), 1),
            'total'   => (int)($res['total'] ?? 0)
        ];
    } catch (PDOException $e) {
        return ['average' => 0, 'total' => 0];
    }
}

/**
 * Распределение оценок (только одобренные)
 */
function getRatingDistribution() {
    global $pdo;
    $dist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    try {
        $stmt = $pdo->query("SELECT rating, COUNT(*) as cnt FROM reviews WHERE is_approved = 1 GROUP BY rating");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dist[$row['rating']] = (int)$row['cnt'];
        }
    } catch (Exception $e) {}
    return $dist;
}

/**
 * Получение отзывов (УНИВЕРСАЛЬНАЯ ФУНКЦИЯ)
 * Принимает массив фильтров ИЛИ старые аргументы для совместимости
 */
function getReviews($arg1 = [], $arg2 = null, $arg3 = null) {
    global $pdo;
    
    $filters = [];
    
    // 1. Обработка аргументов (Совместимость)
    if (is_array($arg1)) {
        // Новый формат: getReviews(['limit' => 3])
        $filters = $arg1;
    } else {
        // Старый формат: getReviews(null, null, 3)
        if ($arg1 !== null) $filters['user_id'] = $arg1;
        if ($arg2 !== null) $filters['status']  = $arg2; // 'approved'
        if ($arg3 !== null) $filters['limit']   = $arg3;
    }

    $where = [];
    $params = [];

    // 2. Логика фильтрации
    // Если задан user_id — показываем ВСЕ отзывы этого юзера (и одобренные, и нет)
    if (isset($filters['user_id'])) {
        $where[] = "r.user_id = ?";
        $params[] = $filters['user_id'];
    } 
    // Иначе — показываем ТОЛЬКО ОДОБРЕННЫЕ (для всех)
    else {
        $where[] = "r.is_approved = 1";
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    $limitClause = isset($filters['limit']) ? "LIMIT " . (int)$filters['limit'] : "";

    try {
        $sql = "
            SELECT 
                r.*,
                u.username, u.first_name, u.last_name,
                p.name as product_name,
                CONCAT(i.first_name, ' ', i.last_name) as installer_name
            FROM reviews r
            LEFT JOIN users u ON r.user_id = u.id
            LEFT JOIN products p ON r.product_id = p.id
            LEFT JOIN installers i ON r.installer_id = i.id
            $whereClause
            ORDER BY r.created_at DESC
            $limitClause
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("getReviews SQL Error: " . $e->getMessage());
        return [];
    }
}
// includes/functions.php

// ... (существующие функции) ...

/**
 * Добавить отзыв на монтажника и пересчитать рейтинг
 */
function addInstallerReview($installer_id, $user_id, $rating, $comment) {
    global $pdo;
    try {
        // 1. Добавляем отзыв
        $stmt = $pdo->prepare("INSERT INTO installer_reviews (installer_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$installer_id, $user_id, $rating, $comment]);
        
        // 2. Пересчитываем рейтинг мастера
        updateInstallerRating($installer_id);
        
        return true;
    } catch (PDOException $e) {
        error_log("Ошибка добавления отзыва на мастера: " . $e->getMessage());
        return false;
    }
}

/**
 * Пересчет и сохранение рейтинга мастера
 */
function updateInstallerRating($installer_id) {
    global $pdo;
    try {
        // Считаем среднее и кол-во
        $stmt = $pdo->prepare("SELECT AVG(rating) as avg, COUNT(*) as cnt FROM installer_reviews WHERE installer_id = ?");
        $stmt->execute([$installer_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $avg = $stats['avg'] ?? 0;
        $cnt = $stats['cnt'] ?? 0;
        
        // Обновляем таблицу installers
        $update = $pdo->prepare("UPDATE installers SET average_rating = ?, review_count = ? WHERE id = ?");
        $update->execute([$avg, $cnt, $installer_id]);
    } catch (PDOException $e) {
        error_log("Ошибка обновления рейтинга мастера: " . $e->getMessage());
    }
}

/**
 * Получить общий средний рейтинг ВСЕХ мастеров
 */
function getGlobalInstallersRating() {
    global $pdo;
    try {
        // Берем среднее из отзывов (или из кэшированных значений в таблице installers)
        // Точнее считать напрямую из таблицы отзывов
        $stmt = $pdo->query("SELECT AVG(rating) as avg FROM installer_reviews");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return round((float)($res['avg'] ?? 0), 1);
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Получить отзывы конкретного мастера
 */
function getInstallerReviews($installer_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT ir.*, u.first_name, u.last_name, u.username 
            FROM installer_reviews ir
            LEFT JOIN users u ON ir.user_id = u.id
            WHERE ir.installer_id = ?
            ORDER BY ir.created_at DESC
        ");
        $stmt->execute([$installer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
// includes/functions.php (добавьте новые функции для видео)

// Получение видео для главной страницы
function getHomepageVideos($limit = 10) {
    global $pdo;
    try {
        $sql = "SELECT id, title, url, thumbnail, description 
                FROM videos 
                WHERE is_active = 1 AND show_on_home = 1 
                ORDER BY sort_order ASC, created_at DESC 
                LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("getHomepageVideos error: " . $e->getMessage());
        return [];
    }
}
/**
 * Получение списка монтажников с рейтингом
 * (Если у вас уже есть getInstallers, используйте её, добавив выборку рейтинга)
 */
function getInstallersWithRating($limit = null) {
    global $pdo;
    try {
        // Убедитесь, что поля average_rating и review_count существуют в таблице installers
        // Если их нет, используйте просто SELECT *
        $sql = "SELECT * FROM installers WHERE is_active = 1 ORDER BY average_rating DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка getInstallersWithRating: " . $e->getMessage());
        return [];
    }
}


// includes/functions.php

// FIX: Use __DIR__ to strictly locate config.php in the same folder
require_once __DIR__ . '/config.php';

/**
 * Получение всех категорий
 */
function getCategories() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения категорий: " . $e->getMessage());
        return [];
    }
}


function getProducts($category_id = null, $limit = null) {
    global $pdo;
    try {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1";
        $params = [];
        if ($category_id) {
            $sql .= " AND p.category_id = ?";
            $params[] = $category_id;
        }
        $sql .= " ORDER BY p.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = (int)$limit;
        }
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения товаров: " . $e->getMessage());
        return [];
    }
}

/**
 * Получение монтажников
 */
function getInstallers() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT i.* FROM installers i WHERE i.is_active = 1 ORDER BY i.rating DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения монтажников: " . $e->getMessage());
        return [];
    }
}

function addToCart($user_id, $product_id, $quantity = 1) {
    global $pdo;
    try {
        // Проверяем существование товара
        $stmt = $pdo->prepare("SELECT id, stock_quantity FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Товар не найден'];
        }
        
        // Проверяем, есть ли уже товар в корзине
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Обновляем количество
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $result = $stmt->execute([$quantity, $user_id, $product_id]);
        } else {
            // Добавляем новый товар
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $result = $stmt->execute([$user_id, $product_id, $quantity]);
        }
        return ['success' => $result, 'message' => $result ? 'Товар добавлен в корзину' : 'Ошибка добавления в корзину'];
    } catch (PDOException $e) {
        error_log("Ошибка добавления в корзину: " . $e->getMessage());
        return ['success' => false, 'message' => 'Ошибка базы данных'];
    }
}

/**
 * Получение корзины пользователя (или гостя из сессии) - ПОЛНОСТЬЮ ПЕРЕПИСАНА
 */
function getCart($user_id) {
    global $pdo;
    
    $cart_items = [];
    
    // Если user_id = 0, работаем с сессией (гость)
    if ($user_id == 0) {
        if (isset($_SESSION['guest_cart']) && !empty($_SESSION['guest_cart'])) {
            $ids = array_keys($_SESSION['guest_cart']);
            if (empty($ids)) return [];

            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $sql = "SELECT p.*, 
                           (SELECT image_path FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as image
                    FROM products p 
                    WHERE p.id IN ($placeholders) AND p.is_active = 1";
            
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute($ids);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($products as $product) {
                    $cart_item = [
                        'id' => $product['id'],
                        'product_id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'old_price' => $product['old_price'],
                        'image' => $product['image'] ?: 'images/products/no-image.png',
                        'quantity' => $_SESSION['guest_cart'][$product['id']]
                    ];
                    $cart_items[] = $cart_item;
                }
            } catch (PDOException $e) {
                error_log("Ошибка получения корзины гостя: " . $e->getMessage());
                return [];
            }
        }
        return $cart_items;
    }

    // Для авторизованного пользователя - ПОЛНОСТЬЮ ПЕРЕПИСАННЫЙ ЗАПРОС
    try {
        $sql = "SELECT c.id as cart_id, 
                       c.product_id, 
                       c.quantity,
                       p.id,
                       p.name, 
                       p.price, 
                       p.old_price,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as image
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ? AND p.is_active = 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($items as $item) {
            $cart_item = [
                'id' => $item['product_id'],
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'old_price' => $item['old_price'],
                'image' => $item['image'] ?: 'images/products/no-image.png',
                'quantity' => $item['quantity']
            ];
            $cart_items[] = $cart_item;
        }
        
        return $cart_items;
        
    } catch (PDOException $e) {
        error_log("Ошибка получения корзины: " . $e->getMessage());
        return [];
    }
}
/**
 * Получение количества товаров в корзине
 */
function getCartCount($user_id = null) {
    global $pdo;
    if ($user_id === 0) {
        if (isset($_SESSION['guest_cart'])) {
            return array_sum($_SESSION['guest_cart']);
        }
        return 0;
    }
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ? (int)$result['total'] : 0;
    } catch (PDOException $e) {
        error_log("Ошибка получения количества товаров в корзине: " . $e->getMessage());
        return 0;
    }
}



/**
 * Создание заявки на монтаж
 */
function createInstallationRequest($data) {
    global $pdo;
    try {
        $sql = "INSERT INTO installer_requests 
                (user_id, installer_id, request_type, description, preferred_date, preferred_time, address, client_name, client_phone, client_email, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $data['user_id'] ?? null,
            $data['installer_id'] ?? null,
            $data['request_type'] ?? 'installation',
            $data['description'] ?? '',
            $data['preferred_date'] ?? null,
            $data['preferred_time'] ?? null,
            $data['address'] ?? '',
            $data['client_name'] ?? 'Клиент',
            $data['client_phone'] ?? '',
            $data['client_email'] ?? null
        ]);
        return $result ? $pdo->lastInsertId() : false;
    } catch (PDOException $e) {
        error_log("Ошибка создания заявки монтажнику: " . $e->getMessage());
        return false;
    }
}

/**
 * Поиск товаров
 */
function searchProducts($query, $category_id = null, $limit = 20) {
    global $pdo;
    try {
        $sql = "SELECT p.*, c.name as category_name,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1 AND 
                      (p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ? OR c.name LIKE ?)";
        $params = ["%$query%", "%$query%", "%$query%", "%$query%"];
        if ($category_id) {
            $sql .= " AND p.category_id = ?";
            $params[] = $category_id;
        }
        $sql .= " ORDER BY p.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT ?";
            $params[] = (int)$limit;
        }
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка поиска товаров: " . $e->getMessage());
        return [];
    }
}


/**
 * Получение изображений товара
 */
function getProductImages($product_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM product_images 
            WHERE product_id = ? 
            ORDER BY is_main DESC, sort_order ASC
        ");
        $stmt->execute([$product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения изображений товара: " . $e->getMessage());
        return [];
    }
}/**
 * --- ЛОГИКА ДЛЯ МОНТАЖНИКОВ И ПОРТФОЛИО ---
 */

// Получение детальной информации о монтажнике
function getInstallerDetails($installer_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM installer_details WHERE installer_id = ?");
    $stmt->execute([$installer_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Добавление фото в портфолио
function addInstallerPortfolioImage($installer_id, $file) {
    global $pdo;
    $uploadDir = __DIR__ . '/../images/installers/portfolio/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'inst_' . $installer_id . '_' . uniqid() . '.' . $ext;
    $target = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target)) {
        $webPath = '/images/installers/portfolio/' . $filename;
        $stmt = $pdo->prepare("INSERT INTO installer_portfolio (installer_id, image_path) VALUES (?, ?)");
        return $stmt->execute([$installer_id, $webPath]);
    }
    return false;
}

// Получение портфолио
function getInstallerPortfolio($installer_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM installer_portfolio WHERE installer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$installer_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Удаление фото из портфолио
function deleteInstallerPortfolioImage($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT image_path FROM installer_portfolio WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();
    
    if ($img) {
        $path = __DIR__ . '/..' . $img['image_path'];
        if (file_exists($path)) unlink($path);
        $stmt = $pdo->prepare("DELETE FROM installer_portfolio WHERE id = ?");
        return $stmt->execute([$id]);
    }
    return false;
}

// Функции для Видео
function getVideos() {
    global $pdo;
    return $pdo->query("SELECT * FROM videos ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);
}

function getVideo($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function saveVideo($data, $id = null) {
    global $pdo;
    if ($id) {
        $stmt = $pdo->prepare("UPDATE videos SET title=?, url=?, description=?, is_active=?, sort_order=? WHERE id=?");
        return $stmt->execute([$data['title'], $data['url'], $data['description'], $data['is_active'], $data['sort_order'], $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO videos (title, url, description, is_active, sort_order) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['title'], $data['url'], $data['description'], $data['is_active'], $data['sort_order']]);
    }
}

function deleteVideo($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
    return $stmt->execute([$id]);
}
/**
 * ==========================================
 * ЛОГИКА: КАТЕГОРИИ (CRUD)
 * ==========================================
 */

function getCategoryById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addCategory($data) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO categories (name, sort_order, is_active) VALUES (?, ?, ?)");
    return $stmt->execute([
        $data['name'], 
        (int)$data['sort_order'], 
        (int)$data['is_active']
    ]);
}

function updateCategory($id, $data) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE categories SET name=?, sort_order=?, is_active=? WHERE id=?");
    return $stmt->execute([
        $data['name'], 
        (int)$data['sort_order'], 
        (int)$data['is_active'], 
        $id
    ]);
}

function deleteCategory($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * ==========================================
 * ЛОГИКА: МОНТАЖНИКИ (CRUD + DETAILS)
 * ==========================================
 */

function getInstallerById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM installers WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Добавление монтажника
function addInstaller($data) {
    global $pdo;
    $sql = "INSERT INTO installers (first_name, last_name, phone, specialization, experience_years, city, hourly_rate, is_active, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['first_name'], 
        $data['last_name'], 
        $data['phone'] ?? null, // Если нет телефона, запишет NULL
        $data['specialization'] ?? '', 
        $data['experience_years'] ?? 0, 
        $data['city'] ?? '', 
        $data['hourly_rate'] ?? 0, 
        $data['is_active'] ?? 1, 
        $data['photo_path'] ?? null
    ]);
    return $pdo->lastInsertId();
}

// Обновление монтажника
function updateInstaller($id, $data) {
    global $pdo;
    
    // Проверяем, передали ли новый путь к фото
    if (!empty($data['photo_path'])) {
        // Если фото обновляем
        $sql = "UPDATE installers SET first_name=?, last_name=?, phone=?, specialization=?, experience_years=?, city=?, hourly_rate=?, is_active=?, photo_path=? WHERE id=?";
        $params = [
            $data['first_name'], 
            $data['last_name'], 
            $data['phone'] ?? null,
            $data['specialization'], 
            $data['experience_years'], 
            $data['city'], 
            $data['hourly_rate'], 
            $data['is_active'], 
            $data['photo_path'], 
            $id
        ];
    } else {
        // Если фото не меняем (оставляем старое)
        $sql = "UPDATE installers SET first_name=?, last_name=?, phone=?, specialization=?, experience_years=?, city=?, hourly_rate=?, is_active=? WHERE id=?";
        $params = [
            $data['first_name'], 
            $data['last_name'], 
            $data['phone'] ?? null,
            $data['specialization'], 
            $data['experience_years'], 
            $data['city'], 
            $data['hourly_rate'], 
            $data['is_active'], 
            $id
        ];
    }
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}


function saveInstallerDetails($installer_id, $description, $services) {
    global $pdo;
    // Проверяем, есть ли запись для этого монтажника
    $stmt = $pdo->prepare("SELECT id FROM installer_details WHERE installer_id = ?");
    $stmt->execute([$installer_id]);
    
    if ($stmt->fetch()) {
        // Если есть -> обновляем
        $sql = "UPDATE installer_details SET description = ?, services_list = ? WHERE installer_id = ?";
    } else {
        // Если нет -> создаем
        $sql = "INSERT INTO installer_details (description, services_list, installer_id) VALUES (?, ?, ?)";
    }
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$description, $services, $installer_id]);
}


function deleteInstaller($id) {
    global $pdo;
    
    try {
        // 1. Начинаем транзакцию, чтобы если что-то пойдет не так, не удалить лишнего
        $pdo->beginTransaction();

        // 2. Удаляем записи из installer_requests (заявки на этого монтажника)
        // Это то, из-за чего была ошибка
        $pdo->prepare("DELETE FROM installer_requests WHERE installer_id = ?")->execute([$id]);

        // 3. Удаляем записи из installer_reviews (отзывы на этого монтажника)
        // Если отзывы есть и в таблице 'reviews', их тоже можно удалить или сделать анонимными
        $pdo->prepare("DELETE FROM installer_reviews WHERE installer_id = ?")->execute([$id]);
        // Очищаем installer_id в общей таблице отзывов, чтобы отзыв остался, но без привязки (или удаляем)
        // Вариант А: Удалить отзывы совсем
        // $pdo->prepare("DELETE FROM reviews WHERE installer_id = ?")->execute([$id]);
        // Вариант Б: Оставить отзыв, но убрать привязку к удаленному мастеру (рекомендуется)
        $pdo->prepare("UPDATE reviews SET installer_id = NULL WHERE installer_id = ?")->execute([$id]);

        // 4. Удаляем детали монтажника (описание, услуги)
        $pdo->prepare("DELETE FROM installer_details WHERE installer_id = ?")->execute([$id]);

        // 5. Удаляем портфолио (файлы и записи)
        // Сначала получаем список файлов, чтобы удалить их с диска
        $stmt = $pdo->prepare("SELECT image_path FROM installer_portfolio WHERE installer_id = ?");
        $stmt->execute([$id]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($images as $img) {
            $path = __DIR__ . '/..' . $img;
            if (file_exists($path)) unlink($path);
        }
        // Теперь удаляем записи из базы
        $pdo->prepare("DELETE FROM installer_portfolio WHERE installer_id = ?")->execute([$id]);

        // 6. Удаляем аватар монтажника с диска
        $stmt = $pdo->prepare("SELECT photo_path FROM installers WHERE id = ?");
        $stmt->execute([$id]);
        $avatar = $stmt->fetchColumn();
        if ($avatar) {
            $path = __DIR__ . '/..' . $avatar;
            if (file_exists($path)) unlink($path);
        }

        // 7. Наконец, удаляем самого монтажника
        $stmt = $pdo->prepare("DELETE FROM installers WHERE id = ?");
        $stmt->execute([$id]);

        // Если все прошло успешно, фиксируем изменения
        $pdo->commit();
        return true;

    } catch (Exception $e) {
        // Если ошибка, отменяем всё
        $pdo->rollBack();
        // Логируем ошибку или выводим её (для отладки можно раскомментировать die)
        error_log("Ошибка удаления монтажника: " . $e->getMessage());
        // die($e->getMessage()); 
        return false;
    }
}


/**
 * ==========================================
 * ЛОГИКА: ТОВАРЫ (CRUD)
 * ==========================================
 */

function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addProduct($data) {
    global $pdo;
    $sql = "INSERT INTO products (name, category_id, price, old_price, sku, stock_quantity, description, short_description, is_active, is_featured, warranty_months, installation_required) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($data)); // Важно: порядок значений в массиве $data должен совпадать!
    return $pdo->lastInsertId();
}

function updateProduct($id, $data) {
    global $pdo;
    $sql = "UPDATE products SET name=?, category_id=?, price=?, old_price=?, sku=?, stock_quantity=?, description=?, short_description=?, is_active=?, is_featured=?, warranty_months=?, installation_required=? WHERE id=?";
    $values = array_values($data);
    $values[] = $id; // добавляем ID в конец для WHERE
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($values);
}

function deleteProduct($id) {
    global $pdo;
    return $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
}
/**
 * ==========================================
 * ЛОГИКА: ИЗОБРАЖЕНИЯ ТОВАРОВ
 * ==========================================
 */



// Удаление изображения товара
function deleteProductImage($image_id) {
    global $pdo;
    
    // 1. Получаем информацию о файле из БД
    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE id = ?");
    $stmt->execute([$image_id]);
    $img = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($img) {
        // 2. Удаляем физический файл
        // Строим полный путь: __DIR__ (includes) -> .. (корень) -> путь из базы (/images/...)
        $filePath = __DIR__ . '/..' . $img['image_path'];
        
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // 3. Удаляем запись из БД
        $delStmt = $pdo->prepare("DELETE FROM product_images WHERE id = ?");
        return $delStmt->execute([$image_id]);
    }
    
    return false;
}
/**
 * ==========================================
 * ЛОГИКА: ЗАГРУЗКА ФАЙЛОВ (СТРОГАЯ)
 * ==========================================
 */

// Добавление изображения товара
function addProductImage($product_id, $file) {
    global $pdo;

    // 1. Разрешенные форматы (только картинки)
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return false; // Неверный формат
    }

    // 2. Пути
    // Папка физически: корень_сайта/images/products/
    $uploadDir = __DIR__ . '/../images/products/';
    // Ссылка для БД: /images/products/имя_файла
    $webDir = '/images/products/';

    // Создаем папку если нет
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    // 3. Генерация имени
    $filename = 'prod_' . $product_id . '_' . uniqid() . '.' . $ext;
    $targetPath = $uploadDir . $filename;
    $dbPath = $webDir . $filename;

    // 4. Перемещение и запись
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Проверяем is_main
        $stmt = $pdo->prepare("SELECT count(*) FROM product_images WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $isMain = ($stmt->fetchColumn() == 0) ? 1 : 0;

        $sql = "INSERT INTO product_images (product_id, image_path, is_main) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$product_id, $dbPath, $isMain]);
    }
    return false;
}

// Добавление/Обновление видео (с загрузкой файла)
// Эту функцию используйте внутри логики video_edit.php или сделайте обертку
function uploadVideoFile($file) {
    // 1. Разрешенные форматы (только видео)
    $allowed = ['mp4', 'webm', 'ogg'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return false; // Неверный формат
    }

    // 2. Пути
    $uploadDir = __DIR__ . '/../videos/';
    $webDir = '/videos/';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    // 3. Генерация имени
    $filename = 'video_' . time() . '_' . uniqid() . '.' . $ext;
    $targetPath = $uploadDir . $filename;
    $dbPath = $webDir . $filename;

    // 4. Перемещение
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $dbPath; // Возвращаем ссылку для записи в БД (например: /videos/video_123.mp4)
    }
    return false;
}
// Получение документов компании, сгруппированных по категориям
function getCompanyDocuments() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM company_documents ORDER BY category, created_at DESC");
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Группируем массив по категориям
    $grouped = [
        'certificate' => [], // Грамоты
        'info' => [],        // Информация
        'other' => []        // Прочее
    ];
    
    foreach ($docs as $doc) {
        if (isset($grouped[$doc['category']])) {
            $grouped[$doc['category']][] = $doc;
        }
    }
    return $grouped;
}

/**
 * ==========================================
 * ЛОГИКА: ДОКУМЕНТЫ КОМПАНИИ
 * ==========================================
 */

// Получить все документы
function getAllDocuments() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM company_documents ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получить документ по ID
function getDocumentById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM company_documents WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Добавить документ
function addDocument($title, $file_path) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO company_documents (title, file_path) VALUES (?, ?)");
    return $stmt->execute([$title, $file_path]);
}

// Удалить документ
function deleteDocument($id) {
    global $pdo;
    
    // Получаем путь к файлу
    $doc = getDocumentById($id);
    if ($doc) {
        // Удаляем файл с диска
        $filePath = __DIR__ . '/..' . $doc['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Удаляем запись из БД
        $stmt = $pdo->prepare("DELETE FROM company_documents WHERE id = ?");
        return $stmt->execute([$id]);
    }
    return false;
}

// Загрузка файла документа
function uploadDocumentFile($file) {
    // Разрешенные форматы
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return false;
    }

    // Пути
    $uploadDir = __DIR__ . '/../images/documents/';
    $webDir = '/images/documents/';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    // Генерация имени
    $filename = 'doc_' . time() . '_' . uniqid() . '.' . $ext;
    $targetPath = $uploadDir . $filename;
    $dbPath = $webDir . $filename;

    // Перемещение
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $dbPath;
    }
    return false;
}

/**
 * Получение содержимого корзины пользователя
 */
function getCartItems($user_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT c.*, p.name, p.price, p.sku 
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Ошибка получения корзины: " . $e->getMessage());
        return [];
    }
}

/**
 * Проверка, является ли пользователь администратором
 */
function isAdmin($user_id = null) {
    global $pdo;
    
    // Если передан ID, проверяем из базы
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && $user['role'] === 'admin';
    }
    
    // Иначе проверяем сессию
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
/**
 * Загрузка изображения товара (возвращает путь или false)
 */
function uploadProductImageFile($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return false;

    $uploadDir = __DIR__ . '/../images/products/';
    $webDir = '/images/products/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $filename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        return $webDir . $filename;
    }
    return false;
}

/**
 * Загрузка аватара монтажника (возвращает путь или false)
 */
function uploadInstallerAvatarFile($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return false;

    $uploadDir = __DIR__ . '/../images/installers/';
    $webDir = '/images/installers/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $filename = 'avatar_' . time() . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        return $webDir . $filename;
    }
    return false;
}

/**
 * Загрузка работы в портфолио монтажника (возвращает путь или false)
 */
function uploadInstallerPortfolioFile($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return false;

    $uploadDir = __DIR__ . '/../images/installers/portfolio/';
    $webDir = '/images/installers/portfolio/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $filename = 'work_' . time() . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        return $webDir . $filename;
    }
    return false;
}
/**
 * Получение хитов продаж (товары с is_featured = 1)
 */
function getFeaturedProducts($limit = 4) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 ORDER BY RAND() LIMIT ?");
        $stmt->execute([(int)$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Получение последних отзывов (для главной страницы)
 */
function getLatestReviews($limit = 3) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT r.id, r.user_id, r.rating, r.comment, r.created_at, 
                   u.username, u.first_name, u.last_name 
            FROM site_reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            WHERE r.is_approved = 1 
            ORDER BY r.created_at DESC 
            LIMIT ?
        ");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
// === ЭТОТ КОД ДОБАВИТЬ В КОНЕЦ functions.php ===

// Функция получения главной картинки товара (для index.php)
if (!function_exists('getProductMainImage')) {
    function getProductMainImage($product_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ? ORDER BY is_main DESC LIMIT 1");
            $stmt->execute([$product_id]);
            $img = $stmt->fetch(PDO::FETCH_ASSOC);
            return $img ? $img['image_path'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
}

// Функция получения хитов продаж
if (!function_exists('getFeaturedProducts')) {
    function getFeaturedProducts($limit = 4) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 LIMIT ?");
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

// Функция получения видео
if (!function_exists('getVideos')) {
    function getVideos($limit = 2) {
        global $pdo;
        try {
            // Проверяем наличие таблицы
            $stmt = $pdo->prepare("SELECT * FROM videos WHERE is_active = 1 ORDER BY created_at DESC LIMIT ?");
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}


// Функция получения списка монтажников (с лимитом)
if (!function_exists('getInstallersList')) {
    function getInstallersList($limit = 4) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM installers WHERE is_active = 1 ORDER BY rating DESC LIMIT ?");
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
// Добавить в functions.php

/**
 * Получение данных пользователя по ID
 */
function getUserById($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Получение заказов пользователя по ID
 */
function getOrdersByUserId($userId) {
    global $pdo;
    // Сортируем от новых к старым
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getUserProductReviews($userId) {
    global $pdo;
    // Убрали p.image, так как этого поля может не быть в БД
    $stmt = $pdo->prepare("
        SELECT pr.*, p.name as product_name, p.id as product_id 
        FROM product_reviews pr 
        JOIN products p ON pr.product_id = p.id 
        WHERE pr.user_id = ? 
        ORDER BY pr.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Получить отзывы о конкретном ТОВАРЕ (для страницы product.php)
 */
function getReviewsByProductId($productId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT pr.*, u.username, u.first_name, u.last_name 
        FROM product_reviews pr 
        JOIN users u ON pr.user_id = u.id 
        WHERE pr.product_id = ? 
        ORDER BY pr.created_at DESC
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Получить отзывы пользователя о МОНТАЖНИКАХ
 */
function getUserInstallerReviews($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT ir.*, i.first_name, i.last_name, i.photo
            FROM installer_reviews ir 
            JOIN installers i ON ir.installer_id = i.id 
            WHERE ir.user_id = ? 
            ORDER BY ir.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Получить отзывы пользователя о МАГАЗИНЕ
 */
function getUserSiteReviews($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM site_reviews WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
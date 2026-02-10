<?php
// ajax/add_to_cart.php
session_start();
// Подключаем конфиг (проверьте путь, если папка ajax вложенная, то ../../includes)
// Судя по структуре: корень -> ajax -> этот файл. Значит ../includes
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

// Получаем данные
$input = json_decode(file_get_contents('php://input'), true);
$product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;
$quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Неверный ID товара']);
    exit;
}

if ($quantity <= 0) $quantity = 1;
$total_count = 0;

try {
    if (isset($_SESSION['user_id'])) {
        // --- АВТОРИЗОВАННЫЙ ---
        $user_id = (int)$_SESSION['user_id'];
        
        $pdo->beginTransaction();

        // Блокируем запись (если есть) или таблицу для этого юзера
        // Пытаемся найти товар
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? FOR UPDATE");
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // ОБНОВЛЯЕМ
            $new_qty = $existing['quantity'] + $quantity;
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$new_qty, $existing['id']]);
        } else {
            // ВСТАВЛЯЕМ
            // На всякий случай удаляем (защита от фантомов)
            $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")->execute([$user_id, $product_id]);
            
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $quantity]);
        }
        
        // Считаем общее кол-во
        $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $total_count = (int)$stmt->fetchColumn();

        $pdo->commit();

    } else {
        // --- ГОСТЬ ---
        if (!isset($_SESSION['guest_cart'])) $_SESSION['guest_cart'] = [];
        
        if (isset($_SESSION['guest_cart'][$product_id])) {
            $_SESSION['guest_cart'][$product_id] += $quantity;
        } else {
            $_SESSION['guest_cart'][$product_id] = $quantity;
        }
        $total_count = array_sum($_SESSION['guest_cart']);
    }

    echo json_encode(['success' => true, 'cart_count' => $total_count]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера']);
}
?>

<?php
// ajax/cart_action.php
session_start();
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$action = isset($_POST['action']) ? $_POST['action'] : 'add';

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Ошибка ID товара']);
    exit;
}

try {
    if ($userId > 0) {
        // --- АВТОРИЗОВАННЫЙ ---
        
        // 1. Проверяем, есть ли УЖЕ этот товар
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? LIMIT 1");
        $stmt->execute([$userId, $productId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($action === 'add') {
            if ($existing) {
                // Если есть - просто плюсуем
                $newQty = $existing['quantity'] + $quantity;
                $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                $stmt->execute([$newQty, $existing['id']]);
            } else {
                // Если нет - вставляем (предварительно удалив возможный мусор)
                $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")->execute([$userId, $productId]);
                
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $productId, $quantity]);
            }
        }
        
        // Получаем итоги для обновления шапки
        $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        $totalQty = (int)$stmt->fetchColumn();
        
        echo json_encode(['success' => true, 'total_qty' => $totalQty]);

    } else {
        // --- ГОСТЬ ---
        if (!isset($_SESSION['guest_cart'])) $_SESSION['guest_cart'] = [];
        
        if ($action === 'add') {
            if (isset($_SESSION['guest_cart'][$productId])) {
                $_SESSION['guest_cart'][$productId] += $quantity;
            } else {
                $_SESSION['guest_cart'][$productId] = $quantity;
            }
        }
        
        echo json_encode(['success' => true, 'total_qty' => array_sum($_SESSION['guest_cart'])]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

<?php
// ajax/update_cart.php
session_start();
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$action = $_POST['action'] ?? '';

if (!$productId || !$action) {
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit;
}

try {
    // Определяем текущее количество
    $currentQty = 0;
    
    if ($userId > 0) {
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ? LIMIT 1");
        $stmt->execute([$userId, $productId]);
        $currentQty = (int)$stmt->fetchColumn();
    } else {
        $currentQty = $_SESSION['guest_cart'][$productId] ?? 0;
    }

    // Вычисляем новое
    $newQty = $currentQty;
    if ($action === 'increase') $newQty++;
    elseif ($action === 'decrease') $newQty--;
    elseif ($action === 'remove') $newQty = 0;

    // Применяем
    if ($userId > 0) {
        if ($newQty > 0) {
            // Если запись была - обновляем
            if ($currentQty > 0) {
                $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$newQty, $userId, $productId]);
            } else {
                // Если вдруг не было (редкий кейс) - вставляем
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $productId, $newQty]);
            }
        } else {
            // Удаляем
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
        }
    } else {
        if ($newQty > 0) {
            $_SESSION['guest_cart'][$productId] = $newQty;
        } else {
            unset($_SESSION['guest_cart'][$productId]);
        }
    }

    // Собираем данные для ответа (Итоги корзины)
    $totalPrice = 0;
    $totalQty = 0;
    $itemSubtotal = 0;

    // Функция для получения цены товара (чтобы не дублировать код)
    function getProductPrice($pdo, $pid) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$pid]);
        return (float)$stmt->fetchColumn();
    }

    // Считаем общую сумму
    if ($userId > 0) {
        $stmt = $pdo->prepare("SELECT c.quantity, p.price, p.id FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $totalQty += $r['quantity'];
            $totalPrice += $r['quantity'] * $r['price'];
            if ($r['id'] == $productId) $itemSubtotal = $r['quantity'] * $r['price'];
        }
    } else {
        if (!empty($_SESSION['guest_cart'])) {
            $ids = array_keys($_SESSION['guest_cart']);
            $in = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($in)");
            $stmt->execute($ids);
            $prices = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            foreach ($_SESSION['guest_cart'] as $pid => $qty) {
                if (isset($prices[$pid])) {
                    $totalQty += $qty;
                    $totalPrice += $qty * $prices[$pid];
                    if ($pid == $productId) $itemSubtotal = $qty * $prices[$pid];
                }
            }
        }
    }

    echo json_encode([
        'success' => true,
        'item_quantity' => $newQty,
        'item_subtotal' => $itemSubtotal,
        'total_price' => $totalPrice,
        'cart_count' => $totalQty
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>

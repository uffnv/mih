<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен.']);
    exit;
}

$userId = $_SESSION['user_id'];
$orderId = (int)$_GET['order_id'];

try {
    // Проверяем, что заказ принадлежит пользователю
    $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $userId]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Заказ не найден.']);
        exit;
    }
    
    // Получаем товары заказа
    $stmt = $pdo->prepare("
        SELECT oi.quantity, oi.price, p.name 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'items' => $items]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных.']);
}

<?php
// ajax/search_products.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = trim($_GET['query']);
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
    
    if (strlen($query) >= 2) {
        $products = searchProducts($query, $category_id, 10);
        echo json_encode([
            'success' => true,
            'products' => $products
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Слишком короткий запрос'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Неверный запрос'
    ]);
}
?>
<?php
// src/CartRepository.php
// Написан в рамках курса Full-Stack — Блок 1: PHP OOP
// Рефакторинг functions.php → репозитории

class CartRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * Получить товары корзины (авторизованный пользователь)
     * userId === 0 → гостевая корзина из SESSION
     */
    public function getItems(int $userId): array
    {
        if ($userId === 0) {
            return $this->getGuestItems();
        }

        try {
            $sql = "SELECT c.id as cart_id,
                           c.product_id,
                           c.quantity,
                           p.id,
                           p.name,
                           p.price,
                           p.old_price,
                           (SELECT image_path FROM product_images
                            WHERE product_id = p.id AND is_main = 1 LIMIT 1) as image
                    FROM cart c
                    JOIN products p ON c.product_id = p.id
                    WHERE c.user_id = ? AND p.is_active = 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            $items = $stmt->fetchAll();

            $cart_items = [];
            foreach ($items as $item) {
                $cart_items[] = [
                    'id'         => $item['product_id'],
                    'product_id' => $item['product_id'],
                    'name'       => $item['name'],
                    'price'      => $item['price'],
                    'old_price'  => $item['old_price'],
                    'image'      => $item['image'] ?: 'images/products/no-image.png',
                    'quantity'   => $item['quantity'],
                ];
            }

            return $cart_items;

        } catch (PDOException $e) {
            error_log("CartRepository::getItems — " . $e->getMessage());
            return [];
        }
    }

    /**
     * Гостевая корзина из SESSION
     */
    private function getGuestItems(): array
    {
        if (empty($_SESSION['guest_cart'])) {
            return [];
        }

        $ids          = array_keys($_SESSION['guest_cart']);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        try {
            $sql = "SELECT p.*,
                           (SELECT image_path FROM product_images
                            WHERE product_id = p.id AND is_main = 1 LIMIT 1) as image
                    FROM products p
                    WHERE p.id IN ($placeholders) AND p.is_active = 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($ids);
            $products = $stmt->fetchAll();

            $cart_items = [];
            foreach ($products as $product) {
                $cart_items[] = [
                    'id'         => $product['id'],
                    'product_id' => $product['id'],
                    'name'       => $product['name'],
                    'price'      => $product['price'],
                    'old_price'  => $product['old_price'],
                    'image'      => $product['image'] ?: 'images/products/no-image.png',
                    'quantity'   => $_SESSION['guest_cart'][$product['id']],
                ];
            }

            return $cart_items;

        } catch (PDOException $e) {
            error_log("CartRepository::getGuestItems — " . $e->getMessage());
            return [];
        }
    }

    /**
     * Количество товаров в корзине
     */
    public function count(int $userId): int
    {
        if ($userId === 0) {
            return isset($_SESSION['guest_cart'])
                ? array_sum($_SESSION['guest_cart'])
                : 0;
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?"
            );
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return $result['total'] ? (int) $result['total'] : 0;
        } catch (PDOException $e) {
            error_log("CartRepository::count — " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Обновить количество товара.
     * quantity <= 0 → удалить товар из корзины
     */
    public function updateQuantity(int $userId, int $productId, int $quantity): bool
    {
        try {
            if ($quantity <= 0) {
                $stmt = $this->pdo->prepare(
                    "DELETE FROM cart WHERE user_id = ? AND product_id = ?"
                );
                return $stmt->execute([$userId, $productId]);
            }

            $stmt = $this->pdo->prepare(
                "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?"
            );
            return $stmt->execute([$quantity, $userId, $productId]);

        } catch (PDOException $e) {
            error_log("CartRepository::updateQuantity — " . $e->getMessage());
            return false;
        }
    }

    /**
     * Очистить корзину пользователя
     */
    public function clear(int $userId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "DELETE FROM cart WHERE user_id = ?"
            );
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("CartRepository::clear — " . $e->getMessage());
            return false;
        }
    }
}

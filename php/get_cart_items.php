<?php
require 'db.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Требуется авторизация']));
}

try {
    // Получаем товары в корзине
    $stmt = $pdo->prepare("
        SELECT id, title, artist, image, price, quantity 
        FROM cart 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $items = $stmt->fetchAll();

    // Считаем общую сумму
    $stmt = $pdo->prepare("
        SELECT SUM(price * quantity) as total 
        FROM cart 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $total = $stmt->fetch()['total'] ?? 0;

    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка базы данных'
    ]);
}
?>
<?php
require 'db.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Требуется авторизация']));
}

$input = json_decode(file_get_contents('php://input'), true);

try {
    // Проверяем текущее количество
    $stmt = $pdo->prepare("
        SELECT quantity FROM cart 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$input['item_id'], $_SESSION['user_id']]);
    $current = $stmt->fetch();

    if (!$current) {
        throw new Exception('Товар не найден');
    }

    $newQuantity = $current['quantity'] + $input['change'];

    if ($newQuantity < 1) {
        // Удаляем если количество стало 0
        $stmt = $pdo->prepare("
            DELETE FROM cart 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$input['item_id'], $_SESSION['user_id']]);
    } else {
        // Обновляем количество
        $stmt = $pdo->prepare("
            UPDATE cart 
            SET quantity = ? 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$newQuantity, $input['item_id'], $_SESSION['user_id']]);
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
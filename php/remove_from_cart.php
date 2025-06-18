<?php
require 'db.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Требуется авторизация']));
}

$input = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $pdo->prepare("
        DELETE FROM cart 
        WHERE id = ? AND user_id = ?
    ");
    $success = $stmt->execute([$input['item_id'], $_SESSION['user_id']]);

    echo json_encode(['success' => $success]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка базы данных'
    ]);
}
?>
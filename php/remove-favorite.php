<?php
header('Content-Type: application/json');
require_once 'db.php';

session_start();

$response = ['success' => false];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Требуется авторизация';
    echo json_encode($response);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND id = ?");
    $stmt->execute([$data['user_id'], $data['item_id']]);
    $response['success'] = ($stmt->rowCount() > 0);
} catch (PDOException $e) {
    $response['message'] = 'Ошибка базы данных';
}

echo json_encode($response);
?>
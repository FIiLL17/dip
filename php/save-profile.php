<?php
header('Content-Type: application/json');
session_start();
require_once 'db.php';

$response = ['success' => false];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Требуется авторизация';
    echo json_encode($response);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];

try {
    $stmt = $db->prepare("
        INSERT INTO user_profiles (user_id, full_name, phone, address)
        VALUES (:user_id, :full_name, :phone, :address)
        ON DUPLICATE KEY UPDATE
        full_name = VALUES(full_name),
        phone = VALUES(phone),
        address = VALUES(address)
    ");
    
    $stmt->execute([
        ':user_id' => $userId,
        ':full_name' => $input['full_name'] ?? null,
        ':phone' => $input['phone'] ?? null,
        ':address' => $input['address'] ?? null
    ]);
    
    $response['success'] = true;
} catch (PDOException $e) {
    $response['message'] = 'Ошибка базы данных: ' . $e->getMessage();
}

echo json_encode($response);
?>
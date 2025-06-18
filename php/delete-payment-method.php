<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

$response = ['success' => false];

if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
    try {
        // Проверяем, что метод оплаты принадлежит пользователю
        $stmt = $db->prepare("DELETE FROM payment_methods WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
        
        $response['success'] = ($stmt->rowCount() > 0);
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'User not authenticated or invalid request';
}

echo json_encode($response);
?>
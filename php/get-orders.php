<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
    exit();
}

// Заглушка - в реальном приложении здесь будет запрос к БД
echo json_encode([
    'success' => true,
    'orders' => []
]);
?>
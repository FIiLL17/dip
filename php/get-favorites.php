<?php
header('Content-Type: application/json');
require_once 'db.php';

session_start();

$response = ['success' => false, 'favorites' => []];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Требуется авторизация';
    echo json_encode($response);
    exit();
}

$userId = $_GET['user_id'];

try {
    $stmt = $db->prepare("
        SELECT f.id, v.title, v.artist, v.price, v.image 
        FROM favorites f
        JOIN vinyls v ON f.vinyl_id = v.id
        WHERE f.user_id = ?
    ");
    $stmt->execute([$userId]);
    $response['favorites'] = $stmt->fetchAll();
    $response['success'] = true;
} catch (PDOException $e) {
    $response['message'] = 'Ошибка базы данных';
}

echo json_encode($response);
?>
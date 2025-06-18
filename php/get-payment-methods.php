<?php
header('Content-Type: application/json');
require_once 'db.php';
session_start();

$response = ['success' => false, 'methods' => []];

if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit;
}

try {
    $stmt = $db->prepare("
        SELECT id, card_type, last_four 
        FROM payment_methods 
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $response['methods'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['success'] = true;
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
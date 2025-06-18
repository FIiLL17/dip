<?php
require 'db.php';
header('Content-Type: application/json');

$userId = $_GET['user_id'] ?? 0;
$response = ['count' => 0];

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM favorites WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    $response['count'] = $result['count'] ?? 0;
} catch (PDOException $e) {
    error_log("Count error: " . $e->getMessage());
}

echo json_encode($response);
?>
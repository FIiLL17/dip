<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Требуется авторизация']));
}

$user_id = $_SESSION['user_id'];
$track_id = $_POST['track_id'];
$action = $_POST['action'];

try {
    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO cart (user_id, track_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $track_id]);
    }
    
    // Всегда возвращаем актуальное количество
    $count = $pdo->query("SELECT COUNT(*) FROM cart WHERE user_id = $user_id")->fetchColumn();
    
    echo json_encode(['success' => true, 'count' => $count]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка базы данных']);
}
?>
<?php
require 'db.php';
session_start();

$response = [
    'cart_count' => 0,
    'favorites_count' => 0
];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    $response['cart_count'] = $pdo->query("SELECT COUNT(*) FROM cart WHERE user_id = $user_id")->fetchColumn();
    $response['favorites_count'] = $pdo->query("SELECT COUNT(*) FROM favorites WHERE user_id = $user_id")->fetchColumn();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
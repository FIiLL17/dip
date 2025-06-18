<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

$response = ['success' => false];

if (isset($_SESSION['user_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['user_id'];
    
    try {
        // Проверяем, существует ли профиль
        $stmt = $db->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        if ($stmt->fetch()) {
            // Обновляем существующий профиль
            $stmt = $db->prepare("UPDATE user_profiles SET name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?");
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['address'],
                $userId
            ]);
        } else {
            // Создаем новый профиль
            $stmt = $db->prepare("INSERT INTO user_profiles (user_id, name, email, phone, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['address']
            ]);
        }
        
        $response['success'] = true;
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'User not authenticated';
}

echo json_encode($response);
?>
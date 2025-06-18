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

$userId = $_SESSION['user_id'];

try {
    // Получаем данные пользователя и профиля
    $stmt = $db->prepare("
        SELECT 
            u.email, 
            u.created_at AS registration_date,
            p.full_name, 
            p.phone, 
            p.address
        FROM users u
        LEFT JOIN user_profiles p ON u.id = p.user_id
        WHERE u.id = ?
    ");
    $stmt->execute([$userId]);
    $data = $stmt->fetch();

    if ($data) {
        $response['success'] = true;
        $response['profile'] = [
            'email' => $data['email'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'registration_date' => $data['registration_date']
        ];
    }
} catch (PDOException $e) {
    $response['message'] = 'Ошибка базы данных';
}

echo json_encode($response);
?>
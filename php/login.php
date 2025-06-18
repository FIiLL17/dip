<?php
header('Content-Type: application/json');
require_once 'db.php';

// Обработка ошибок
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

$response = ['success' => false, 'message' => ''];

try {
    // Получаем JSON данные
    $json = file_get_contents('php://input');
    if (!$json) {
        throw new Exception('No input data');
    }
    
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    // Валидация данных
    if (empty($data['email'])) {
        throw new Exception('Email is required');
    }

    if (empty($data['password'])) {
        throw new Exception('Password is required');
    }

    // Поиск пользователя
    $stmt = $db->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new Exception('User not found');
    }

    // Проверка пароля
    if (!password_verify($data['password'], $user['password'])) {
        throw new Exception('Invalid password');
    }

    // Начинаем сессию
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];

    $response = [
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ]
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Убедимся, что вывод только JSON
echo json_encode($response);
exit();
?>
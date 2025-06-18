<?php
header('Content-Type: application/json');
require_once 'db.php';

$response = ['success' => false, 'message' => ''];

try {
    // Получаем RAW JSON данные
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Валидация данных
    if (empty($data['name'])) {
        throw new Exception('Имя обязательно для заполнения');
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Некорректный email');
    }

    if (strlen($data['password']) < 6) {
        throw new Exception('Пароль должен содержать минимум 6 символов');
    }

    if ($data['password'] !== $data['confirm_password']) {
        throw new Exception('Пароли не совпадают');
    }

    // Проверка существования пользователя
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    
    if ($stmt->fetch()) {
        throw new Exception('Пользователь с таким email уже существует');
    }

    // Хеширование пароля
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    // Вставка в БД
    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$data['name'], $data['email'], $hashedPassword]);

    $response['success'] = true;
    $response['message'] = 'Регистрация успешна!';
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
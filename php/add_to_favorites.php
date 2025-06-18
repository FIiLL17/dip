<?php
require 'db.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Валидация данных
$required = ['title', 'artist', 'image', 'price'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        echo json_encode(['success' => false, 'message' => "Не указано поле $field"]);
        exit;
    }
}

try {
    // Проверяем существование пользователя
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Пользователь не существует');
    }

    // Добавляем запись
    $stmt = $pdo->prepare("
        INSERT INTO favorites 
        (user_id, title, artist, image, price) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $_SESSION['user_id'],
        $input['title'],
        $input['artist'],
        $input['image'],
        $input['price']
    ]);

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Успешно добавлено' : 'Ошибка добавления'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка базы данных',
        'error_code' => $e->getCode(),
        'error_info' => $e->errorInfo
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
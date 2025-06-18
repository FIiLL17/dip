<?php
require 'db.php';
header('Content-Type: application/json');

// Включаем подробное логирование ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'Требуется авторизация']));
}

// Получаем входные данные
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    die(json_encode(['success' => false, 'message' => 'Неверный формат данных']));
}

// Логирование для отладки
error_log("Данные для добавления в корзину: " . print_r($input, true));
error_log("ID пользователя: " . $_SESSION['user_id']);

try {
    // Проверяем, есть ли уже такой альбом в корзине
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND title = ?");
    $stmt->execute([$_SESSION['user_id'], $input['title']]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Увеличиваем количество, если уже есть
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        $success = $stmt->execute([$existing['id']]);
    } else {
        // Добавляем новый элемент
        $stmt = $pdo->prepare("
            INSERT INTO cart 
            (user_id, title, artist, image, price) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $success = $stmt->execute([
            $_SESSION['user_id'],
            $input['title'],
            $input['artist'] ?? '',
            $input['image'] ?? '',
            $input['price'] ?? 0
        ]);
    }

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Успешно добавлено в корзину' : 'Не удалось добавить'
    ]);

} catch (PDOException $e) {
    error_log("Ошибка базы данных: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка базы данных',
        'error_code' => $e->getCode(),
        'error_info' => $e->errorInfo
    ]);
}
?>
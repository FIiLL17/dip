<?php
header('Content-Type: application/json');
session_start();
require_once 'db.php';

$response = ['success' => false, 'error' => null];

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'Требуется авторизация';
    echo json_encode($response);
    exit;
}

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['error'] = 'Неверный метод запроса';
    echo json_encode($response);
    exit;
}

// Чтение входных данных
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $response['error'] = 'Неверный формат данных';
    echo json_encode($response);
    exit;
}

// Валидация данных
$requiredFields = ['card_number', 'card_holder', 'card_expiry', 'card_cvv'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        $response['error'] = 'Заполните все поля';
        echo json_encode($response);
        exit;
    }
}

// Подготовка данных
$userId = $_SESSION['user_id'];
$lastFour = substr(preg_replace('/\D/', '', $input['card_number']), -4);

try {
    // Определение типа карты (VISA, Mastercard и т.д.)
    $cardType = 'UNKNOWN';
    if (preg_match('/^4/', $input['card_number'])) $cardType = 'VISA';
    elseif (preg_match('/^5[1-5]/', $input['card_number'])) $cardType = 'MASTERCARD';

    // Сохранение в БД
    $stmt = $db->prepare("
        INSERT INTO payment_methods 
        (user_id, card_number, card_holder, card_expiry, card_cvv, card_type, last_four)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $userId,
        password_hash($input['card_number'], PASSWORD_DEFAULT), // Хеширование номера
        $input['card_holder'],
        $input['card_expiry'],
        password_hash($input['card_cvv'], PASSWORD_DEFAULT),    // Хеширование CVV
        $cardType,
        $lastFour
    ]);

    $response['success'] = true;
} catch (PDOException $e) {
    $response['error'] = 'Ошибка БД: ' . $e->getMessage();
}

echo json_encode($response);
?>
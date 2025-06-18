<?php
session_start();
header('Content-Type: application/json');
require_once 'db.php';

$response = ['success' => false];

if (isset($_SESSION['user_id'])) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        // Определяем тип карты по номеру
        $cardNumber = str_replace(' ', '', $data['card_number']);
        $cardType = 'UNKNOWN';
        
        if (preg_match('/^4/', $cardNumber)) $cardType = 'VISA';
        elseif (preg_match('/^5[1-5]/', $cardNumber)) $cardType = 'MASTERCARD';
        elseif (preg_match('/^3[47]/', $cardNumber)) $cardType = 'AMEX';
        
        $stmt = $db->prepare("INSERT INTO payment_methods (user_id, card_number, card_type, card_expiry, card_holder, last_four) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $data['card_number'],
            $cardType,
            $data['card_expiry'],
            $data['card_holder'],
            substr($cardNumber, -4)
        ]);
        
        $response['success'] = true;
    } catch (PDOException $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'User not authenticated';
}

echo json_encode($response);
?>
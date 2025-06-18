<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'klouddisc';
$username = 'root';
$password = '';

try {
    // Универсальное подключение
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Для совместимости со старым кодом
    $db = $pdo;
    
} catch (PDOException $e) {
    // Логирование ошибки
    error_log('DB Connection Error: ' . $e->getMessage());
    
    // Для API-запросов
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        header('Content-Type: application/json');
        die(json_encode([
            'success' => false,
            'message' => 'Database error',
            'error_code' => $e->getCode()
        ]));
    }
    
    // Для обычных страниц
    die('Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.');
}
?>
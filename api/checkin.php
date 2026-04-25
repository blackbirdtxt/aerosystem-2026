<?php
// api/checkin.php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    responder(['error' => 'No autorizado'], 401);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $sql = "EXEC sp_CheckIn ?, ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$input['idreserva'], $_SESSION['user_id']]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    responder($result);
}
?>
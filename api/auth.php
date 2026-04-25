<?php
// api/auth.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $mail = $input['mail'] ?? '';
    $clave = $input['clave'] ?? '';

    $stmt = $pdo->prepare("
        SELECT idpasajero, nombre, apaterno, mail 
        FROM pasajero 
        WHERE mail = ? AND clave = ?
    ");
    $stmt->execute([$mail, $clave]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        session_start();
        $_SESSION['user_id'] = $user['idpasajero'];
        responder(['success' => true, 'user' => $user]);
    } else {
        responder(['error' => 'Credenciales inválidas'], 401);
    }
}
?>
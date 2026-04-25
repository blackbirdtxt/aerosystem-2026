<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) responder(['error' => 'Unauthorized'], 401);

// POST: Registrar equipaje
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("
        INSERT INTO equipaje (idreserva, idpasajero, idtipo, peso_real, color, descripcion, etiqueta_codigo)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $codigo = 'LIM-' . date('Ymd') . '-' . rand(1000,9999);
    $stmt->execute([
        $input['idreserva'],
        $_SESSION['user_id'],
        $input['idtipo'],
        $input['peso_real'],
        $input['color'],
        $input['descripcion'],
        $codigo
    ]);
    responder(['codigo' => $codigo, 'mensaje' => 'Equipaje registrado']);
}

// GET: Equipaje usuario + tracking
if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $stmt = $pdo->prepare("SELECT * FROM vw_EquipajeTracking WHERE etiqueta_codigo = ?");
    $stmt->execute([$codigo]);
    responder($stmt->fetch(PDO::FETCH_ASSOC) ?: null);
}
?>
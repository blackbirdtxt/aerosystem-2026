<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) responder(['error' => 'Unauthorized'], 401);

// POST: Procesar pago
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $sql = "EXEC sp_ProcesarPago ?, ?, ?, ?, ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $input['idreserva'],
        $_SESSION['user_id'],
        $input['monto'],
        $input['tipo_documento'],
        $input['numcomprobante']
    ]);
    responder($stmt->fetch(PDO::FETCH_ASSOC));
}

// GET: Reservas usuario
$stmt = $pdo->prepare("
    SELECT r.*, v.numero_vuelo, apo.nombre as origen, apd.nombre as destino, p.monto as pagado
    FROM reserva r
    JOIN vuelo v ON v.idreserva = r.idreserva
    JOIN aeropuerto apo ON apo.idaeropuerto = v.origen
    JOIN aeropuerto apd ON apd.idaeropuerto = v.destino
    LEFT JOIN pago p ON p.idreserva = r.idreserva AND p.idpasajero = ?
");
$stmt->execute([$_SESSION['user_id']]);
responder($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
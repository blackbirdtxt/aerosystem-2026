<?php
require_once 'config.php';
session_start();

$idcheckin = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("
    SELECT ci.*, p.nombre + ' ' + p.apaterno as pasajero, v.numero_vuelo
    FROM check_in ci
    JOIN pasajero p ON p.idpasajero = ci.idpasajero
    JOIN reserva r ON r.idreserva = ci.idreserva
    JOIN vuelo v ON v.idreserva = r.idreserva
    WHERE ci.idcheckin = ?
");
$stmt->execute([$idcheckin]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    // HTML → PDF simple
    $html = "
    <h1>BOARDING PASS</h1>
    <p>Vuelo: {$data['numero_vuelo']}</p>
    <p>Pasajero: {$data['pasajero']}</p>
    <p>Asiento: {$data['asiento_asignado']}</p>
    <p>Puerta: {$data['puerta_embarque']}</p>
    <qr-code value='{$data['idcheckin']}'></qr-code>
    ";
    responder(['boarding' => $html]);
}
?>
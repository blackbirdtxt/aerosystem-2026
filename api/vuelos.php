<?php
// api/vuelos.php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $origen = $_GET['origen'] ?? '';
    $destino = $_GET['destino'] ?? '';
    $fecha = $_GET['fecha'] ?? date('Y-m-d', strtotime('+1 day'));
    
    $sql = "EXEC sp_BuscarVuelos ?, ?, ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$origen, $destino, $fecha]);
    
    $vuelos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    responder($vuelos);
}
?>
<?php
// api/panel.php - Dashboard Aeropuerto
require_once 'config.php';

$stmt = $pdo->query("SELECT * FROM vw_PanelAeropuerto ORDER BY fecha_salida ASC");
$vuelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

responder($vuelos);
?>
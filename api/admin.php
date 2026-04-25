<?php
require_once 'config.php';
// Admin sin auth (producción agregar)

// Estadísticas
$stats = [
    'vuelos_hoy' => $pdo->query("SELECT COUNT(*) FROM vw_PanelAeropuerto")->fetchColumn(),
    'reservas' => $pdo->query("SELECT COUNT(*) FROM reserva WHERE estado='Confirmada'")->fetchColumn(),
    'pasajeros' => $pdo->query("SELECT COUNT(*) FROM pasajero")->fetchColumn(),
    'ingresos' => $pdo->query("SELECT SUM(monto) FROM pago")->fetchColumn()
];

// Vuelos próximos 24h
$vuelos = $pdo->query("SELECT * FROM vw_PanelAeropuerto ORDER BY fecha_salida")->fetchAll();
responder(['stats' => $stats, 'vuelos' => $vuelos]);
?>
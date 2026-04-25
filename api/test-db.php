<?php
// api/test-windows.php - TU CONFIG EXACTA
header('Content-Type: text/html; charset=utf-8');

$servidor = 'LAPTOP-HPAR\SQLEXPRESS';
$db = 'AEROSYSTEM_DB';

echo "<h1>🟢 TEST WINDOWS AUTH - AEROSYSTEM_DB</h1><hr>";

try {
    $dsn = "sqlsrv:Server=$servidor;Database=$db;TrustServerCertificate=yes";
    $pdo = new PDO($dsn, '', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::SQLSRV_ATTR_TRUST_SERVER_CERTIFICATE => true
    ]);
    
    echo "<span style='color:green; font-size:24px'>✅ CONEXIÓN WINDOWS AUTH OK</span><br><br>";
    
    // Test tablas
    $tablas = ['vuelo', 'pasajero', 'reserva', 'aeropuerto'];
    foreach ($tablas as $tabla) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM $tabla");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "<span style='color:green'>✅ $tabla: $total registros</span><br>";
    }
    
    // Test vista panel
    $stmt = $pdo->query("SELECT TOP 5 * FROM vw_PanelAeropuerto");
    $vuelos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>✈️ VUELOS PANEL:</h3>";
    echo "<pre>" . print_r($vuelos, true) . "</pre>";
    
    // Test SP
    $stmt = $pdo->query("SELECT COUNT(*) as sp FROM sys.procedures WHERE name = 'sp_BuscarVuelos'");
    $sp = $stmt->fetch(PDO::FETCH_ASSOC)['sp'];
    echo $sp ? "<span style='color:green; font-size:20px'>✅ STORED PROCEDURES OK</span>" : "<span style='color:red'>❌ SP NO ENCONTRADOS</span>";
    
} catch (Exception $e) {
    echo "<span style='color:red; font-size:20px'>❌ ERROR: " . $e->getMessage() . "</span>";
    echo "<br><strong>Verifica:</strong><br>";
    echo "1. SQL Server (LAPTOP-HPAR\SQLEXPRESS) corriendo<br>";
    echo "2. TCP/IP habilitado puerto 1433<br>";
    echo "3. DB AEROSYSTEM_DB existe";
}
?>
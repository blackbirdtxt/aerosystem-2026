<?php
// api/test-windows.php - VERSIÓN CORREGIDA ✅
header('Content-Type: text/html; charset=utf-8');

$servidor = 'LAPTOP-HPAR\SQLEXPRESS';
$db = 'AEROSYSTEM_DB';

echo "<h1>🟢 TEST WINDOWS AUTH - AEROSYSTEM_DB</h1><hr>";

try {
    // OPCIÓN 1: TrustServerCertificate en DSN
    $dsn = "sqlsrv:Server=$servidor;Database=$db;TrustServerCertificate=yes";
    $pdo = new PDO($dsn, '', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<span style='color:green; font-size:28px'>✅ CONEXIÓN WINDOWS AUTH PERFECTA</span><br><br>";
    
    // Test tablas
    $tablas = ['vuelo', 'pasajero', 'reserva', 'aeropuerto'];
    foreach ($tablas as $tabla) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM $tabla");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "<span style='color:green'>✅ $tabla: <strong>$total</strong> registros</span><br>";
    }
    
    // Test vista panel
    $stmt = $pdo->query("SELECT TOP 5 numero_vuelo, estado, fecha_salida FROM vw_PanelAeropuerto ORDER BY fecha_salida");
    $vuelos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>✈️ PANEL VUELOS (Live):</h3>";
    echo "<table border='1' style='border-collapse:collapse'>";
    echo "<tr><th>Vuelo</th><th>Estado</th><th>Salida</th></tr>";
    foreach ($vuelos as $vuelo) {
        echo "<tr><td>{$vuelo['numero_vuelo']}</td><td>{$vuelo['estado']}</td><td>{$vuelo['fecha_salida']}</td></tr>";
    }
    echo "</table>";
    
    // Test SP
    $stmt = $pdo->query("SELECT COUNT(*) as sp FROM sys.procedures WHERE name IN ('sp_BuscarVuelos','sp_ReservarVuelo')");
    $sp = $stmt->fetch(PDO::FETCH_ASSOC)['sp'];
    echo "<br><span style='color:" . ($sp ? "green" : "red") . "; font-size:20px'>" . 
         ($sp ? "✅ STORED PROCEDURES OK ($sp)" : "❌ SP NO ENCONTRADOS") . "</span>";
    
    echo "<hr><span style='color:blue; font-size:18px'>🎉 <strong>¡BASE DE DATOS LISTA!</strong> Ahora prueba panel-aeropuerto.html</span>";
    
} catch (Exception $e) {
    echo "<span style='color:red; font-size:20px'>❌ ERROR: " . $e->getMessage() . "</span><br><br>";
    echo "<strong>SOLUCIONES:</strong><br>";
    echo "1. Verificar SQL Server (LAPTOP-HPAR\SQLEXPRESS) corriendo<br>";
    echo "2. Descargar: <a href='https://github.com/Microsoft/msphpsql/releases'>SQLSRV PHP Driver</a><br>";
    echo "3. php.ini: extension=sqlsrv y extension=pdo_sqlsrv";
}
?>
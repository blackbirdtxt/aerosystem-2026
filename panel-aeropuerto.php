<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroSystem Pro - Panel Aeropuerto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass { backdrop-filter: blur(20px); }
        .glow { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 min-h-screen">
    
    <!-- Header -->
    <header class="glass bg-white/10 sticky top-0 z-50 backdrop-blur-lg border-b border-white/20">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-plane-departure text-3xl text-blue-400"></i>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
                        AeroSystem Pro
                    </h1>
                </div>
                <div class="text-white text-sm">Actualizado: <span id="fecha"></span></div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="glass bg-white/10 p-6 rounded-2xl text-center glow">
                <i class="fas fa-plane text-3xl text-green-400 mb-2"></i>
                <div class="text-2xl font-bold text-white" id="totalVuelos">0</div>
                <div class="text-blue-200">Vuelos Hoy</div>
            </div>
            <div class="glass bg-white/10 p-6 rounded-2xl text-center glow">
                <i class="fas fa-clock text-3xl text-yellow-400 mb-2"></i>
                <div class="text-2xl font-bold text-white" id="retrasados">0</div>
                <div class="text-blue-200">Retrasados</div>
            </div>
            <div class="glass bg-white/10 p-6 rounded-2xl text-center glow">
                <i class="fas fa-users text-3xl text-orange-400 mb-2"></i>
                <div class="text-2xl font-bold text-white" id="embarcando">0</div>
                <div class="text-blue-200">Embarcando</div>
            </div>
            <div class="glass bg-white/10 p-6 rounded-2xl text-center glow">
                <i class="fas fa-exclamation-triangle text-3xl text-red-400 mb-2"></i>
                <div class="text-2xl font-bold text-white" id="cancelados">0</div>
                <div class="text-blue-200">Cancelados</div>
            </div>
        </div>
                    <!-- AGREGAR DESPUÉS stats cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <canvas id="graficoVuelos" width="200" height="100"></canvas>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            // Gráficos
            fetch('api/admin.php')
                .then(r=>r.json())
                .then(data => {
                    new Chart(document.getElementById('graficoVuelos'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Vuelos', 'Reservas', 'Pasajeros'],
                            datasets: [{
                                data: [data.stats.vuelos_hoy, data.stats.reservas, data.stats.pasajeros],
                                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b']
                            }]
                        }
                    });
                });
            </script>

        <!-- Vuelos Table -->
        <div class="glass bg-white/5 backdrop-blur-xl rounded-3xl p-8 border border-white/20">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-white flex items-center">
                    <i class="fas fa-list mr-3 text-blue-400"></i>
                    Panel de Vuelos en Tiempo Real
                </h2>
                <button onclick="actualizarDatos()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-semibold transition-all glow">
                    <i class="fas fa-sync-alt mr-2"></i>Actualizar
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="tablaVuelos">
                    <thead>
                        <tr class="bg-white/5 text-blue-200">
                            <th class="p-4 text-left rounded-tl-xl">Vuelo</th>
                            <th class="p-4 text-left">Aerolínea</th>
                            <th class="p-4 text-left">Origen → Destino</th>
                            <th class="p-4 text-center">Salida</th>
                            <th class="p-4 text-center">Llegada</th>
                            <th class="p-4 text-center">Estado</th>
                            <th class="p-4 text-center rounded-tr-xl">Tiempo</th>
                        </tr>
                    </thead>
                    <tbody id="vuelosBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let vuelos = [];

        // Actualizar fecha
        document.getElementById('fecha').textContent = new Date().toLocaleString('es-PE');

        // Cargar datos
        async function cargarDatos() {
            try {
                const response = await fetch('api/panel.php');
                vuelos = await response.json();
                renderizarTabla();
                actualizarStats();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function renderizarTabla() {
            const tbody = document.getElementById('vuelosBody');
            tbody.innerHTML = '';

            vuelos.forEach((vuelo, index) => {
                const minutosRestantes = Math.floor(vuelo.minutos_para_salida / 60);
                const estadoClass = {
                    'Programado': 'bg-green-500',
                    'Abordando': 'bg-orange-500',
                    'En vuelo': 'bg-blue-500',
                    'Aterrizado': 'bg-gray-500',
                    'Cancelado': 'bg-red-500',
                    'Retrasado': 'bg-yellow-500'
                }[vuelo.estado] || 'bg-gray-500';

                const tiempoTexto = minutosRestantes > 0 
                    ? `-${minutosRestantes}h`
                    : '¡Ya salió!';

                tbody.innerHTML += `
                    <tr class="hover:bg-white/10 transition-all border-b border-white/10">
                        <td class="p-4 font-bold text-white">${vuelo.numero_vuelo}</td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-white/10 text-white">
                                ${vuelo.aerolinea}
                            </span>
                        </td>
                        <td class="p-4">
                            <div>
                                <div class="font-semibold text-white">${vuelo.origen.slice(0,3)}</div>
                                <div class="text-blue-300 text-sm">→ ${vuelo.destino.slice(0,3)}</div>
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <div class="text-white font-mono">${new Date(vuelo.fecha_salida).toLocaleTimeString('es-PE', {hour: '2-digit', minute: '2-digit'})}</div>
                        </td>
                        <td class="p-4 text-center">
                            <div class="text-white font-mono">${new Date(vuelo.fecha_llegada).toLocaleTimeString('es-PE', {hour: '2-digit', minute: '2-digit'})}</div>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold ${estadoClass} text-white">
                                ${vuelo.estado}
                            </span>
                        </td>
                        <td class="p-4 text-center font-mono text-lg font-bold ${minutosRestantes < 30 ? 'text-red-400 animate-pulse' : 'text-blue-300'}">
                            ${tiempoTexto}
                        </td>
                    </tr>
                `;
            });
        }

        function actualizarStats() {
            const stats = {
                totalVuelos: vuelos.length,
                retrasados: vuelos.filter(v => v.estado === 'Retrasado').length,
                embarcados: vuelos.filter(v => v.estado === 'Abordando').length,
                cancelados: vuelos.filter(v => v.estado === 'Cancelado').length
            };

            document.getElementById('totalVuelos').textContent = stats.totalVuelos;
            document.getElementById('retrasados').textContent = stats.retrasados;
            document.getElementById('embarcando').textContent = stats.embarcados;
            document.getElementById('cancelados').textContent = stats.cancelados;
        }

        function actualizarDatos() {
            cargarDatos();
        }

        // Auto-refresh cada 30 segundos
        setInterval(actualizarDatos, 30000);
        
        // Cargar inicial
        cargarDatos();
    </script>
</body>
</html>
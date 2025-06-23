@extends('layouts.app')

@section('content')

    <body class="bg-gray-100 font-sans">

        <!-- Navigation -->
        <nav class="bg-white shadow p-4 flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600 hover:underline">IoT Dashboard</a>
            <div class="space-x-4">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-500">Home</a>
                <a href="{{ route('riwayat') }}" class="text-gray-700 hover:text-blue-500">Riwayat</a>
                <a href="{{ route('profil') }}" class="text-gray-700 hover:text-blue-500">Profil</a>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Notifikasi Visual -->
            <div class="col-span-1 bg-white rounded-xl p-4 shadow">
                <h2 class="text-lg font-semibold mb-2">Status Notifikasi</h2>
                <div id="notificationBox" class="w-full h-24 rounded-xl flex items-center justify-center text-white text-xl font-bold
            @if(isset($logs) && count($logs))
                  @if($logs->last()->status === 'merah') bg-red-500
                  @elseif($logs->last()->status === 'kuning') bg-yellow-400
                      @else bg-green-500
                  @endif
            @else
                  bg-gray-300
            @endif">
                    {{ isset($logs) && count($logs) ? ucfirst($logs->last()->status) : 'Tidak Ada Data' }}
                </div>
            </div>

            <!-- Grafik -->
            <div class="col-span-2 bg-white rounded-xl p-4 shadow">
                <h2 class="text-lg font-semibold mb-2">Grafik Suhu & Asap Harian</h2>
                <canvas id="sensorChart" height="100"></canvas>
            </div>

        </div>

        <!-- Filter dan Tabel Riwayat -->
        <div class="p-6">
            <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
                <label for="status" class="mr-2 font-semibold">Filter Status:</label>
                <select name="status" id="status" onchange="this.form.submit()" class="border rounded p-1">
                    <option value="">Semua</option>
                    <option value="merah" {{ request('status') == 'merah' ? 'selected' : '' }}>Bahaya</option>
                    <option value="kuning" {{ request('status') == 'kuning' ? 'selected' : '' }}>Waspada</option>
                    <option value="hijau" {{ request('status') == 'hijau' ? 'selected' : '' }}>Aman</option>
                </select>
            </form>

            <div class="bg-white rounded-xl shadow p-4">
                <h2 class="text-lg font-semibold mb-4">Riwayat Log Peringatan</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="px-4 py-2">Waktu</th>
                                <th class="px-4 py-2">Suhu</th>
                                <th class="px-4 py-2">Kelembapan</th>
                                <th class="px-4 py-2">Gas</th>
                                <th class="px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $log->created_at }}</td>
                                        <td class="px-4 py-2">{{ $log->suhu }}°C</td>
                                        <td class="px-4 py-2">{{ $log->kelembapan }}%</td>
                                        <td class="px-4 py-2">{{ $log->gas }} ppm</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded text-white text-sm
                                @if($log->status == 'merah') bg-red-500
                                @elseif($log->status == 'kuning') bg-yellow-500
                                    @else bg-green-500
                                @endif">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ChartJS -->
        <script>
            const chartLabels = @json($sensorData['labels']);
            const suhuData = @json($sensorData['suhu']);
            const gasData = @json($sensorData['gas']);

            const ctx = document.getElementById('sensorChart').getContext('2d');
            const sensorChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Suhu (°C)',
                            data: suhuData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false,
                            tension: 0.3
                        },
                        {
                            label: 'Gas (ppm)',
                            data: gasData,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            fill: false,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            });
        </script>

        <!-- AJAX Polling Notifikasi Real-time -->
        <script>
            let lastNotified = null;

            function checkLatestLog() {
                fetch('/check-latest-log')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'merah') {
                            if (lastNotified !== data.created_at) {
                                showAlert(data);
                                lastNotified = data.created_at;
                            }
                        }
                    });
            }

            function showAlert(data) {
                const box = document.createElement('div');
                box.innerText = `⚠️ PERINGATAN! Status: MERAH\nSuhu: ${data.suhu}°C, Gas: ${data.gas} ppm`;
                box.className = 'fixed top-5 right-5 bg-red-600 text-white px-4 py-2 rounded shadow-lg z-50';
                document.body.appendChild(box);

                setTimeout(() => box.remove(), 5000);
            }

            setInterval(checkLatestLog, 5000);
        </script>
        <script>
            // Subscribe ke MQTT via WebSocket
            const client = new Paho.MQTT.Client("broker.emqx.io", 8084, "laravel_dashboard");

            client.onMessageArrived = function (message) {
                const data = JSON.parse(message.payloadString);
                updateDashboard(data);
            };

            function updateDashboard(data) {
                // Update notifikasi status
                const statusBox = document.getElementById('notificationBox');
                statusBox.className = `w-full h-24 rounded-xl flex items-center justify-center text-white text-xl font-bold ${data.status === 'merah' ? 'bg-red-500' :
                        data.status === 'kuning' ? 'bg-yellow-400' : 'bg-green-500'
                    }`;
                statusBox.innerText = data.status;

                // Update chart
                chart.data.labels.push(new Date().toLocaleTimeString());
                chart.data.datasets[0].data.push(data.suhu);
                chart.data.datasets[1].data.push(data.gas);
                chart.update();
            }

            client.connect({
                onSuccess: function () {
                    client.subscribe("wokwi/laravel");
                }
            });
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.1.0/paho-mqtt.min.js"></script>
        <script>
            // MQTT Client
            const client = new Paho.MQTT.Client("broker.emqx.io", 8084, "laravel-dashboard");

            client.onMessageArrived = function (message) {
                const data = JSON.parse(message.payloadString);
                updateDashboard(data);
            };

            function updateDashboard(data) {
                // Update notification box
                const statusBox = document.getElementById('notificationBox');
                statusBox.className = `w-full h-24 rounded-xl flex items-center justify-center text-white text-xl font-bold ${data.status.toLowerCase() === 'merah' ? 'bg-red-500' :
                        data.status.toLowerCase() === 'kuning' ? 'bg-yellow-400' : 'bg-green-500'
                    }`;
                statusBox.innerText = data.status;

                // Update chart
                chart.data.labels.push(new Date().toLocaleTimeString());
                chart.data.datasets[0].data.push(data.suhu);
                chart.data.datasets[1].data.push(data.gas);
                if (chart.data.labels.length > 10) {
                    chart.data.labels.shift();
                    chart.data.datasets[0].data.shift();
                    chart.data.datasets[1].data.shift();
                }
                chart.update();

                // Update table
                addToLogTable(data);
            }

            // Connect to MQTT broker
            client.connect({
                onSuccess: function () {
                    client.subscribe("ranindya/iot/sensor/data");
                },
                onFailure: function () {
                    console.error("MQTT connection failed");
                }
            });

            // Fallback dengan WebSocket
            const echo = new Echo({
                broadcaster: 'pusher',
                key: 'your-pusher-key',
                cluster: 'mt1',
                encrypted: true
            });

            echo.channel('sensor-updates')
                .listen('SensorUpdated', (data) => {
                    updateDashboard(data.sensorData);
                });
        </script>
    </body>
@endsection

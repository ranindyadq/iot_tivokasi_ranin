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

</body>
@endsection

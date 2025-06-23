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
  <div class="p-6">
    <form method="GET" action="{{ route('riwayat') }}" class="mb-4">
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

</body>
@endsection

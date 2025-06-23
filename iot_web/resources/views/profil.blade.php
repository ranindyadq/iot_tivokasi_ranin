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
  <div class="p-6 max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <!-- Header Profil -->
      <div class="bg-blue-600 p-6 text-white">
        <div class="flex items-center space-x-4">
          <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center text-blue-600 text-3xl font-bold">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
          </div>
          <div>
            <h1 class="text-2xl font-bold">{{ Auth::user()->name }}</h1>
            <p class="text-blue-100">{{ Auth::user()->email }}</p>
            <p class="text-sm mt-1">Bergabung sejak: {{ Auth::user()->created_at->format('d M Y') }}</p>
          </div>
        </div>
      </div>

      <!-- Form Profil -->
      <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Informasi Profil</h2>

        @if(session('success'))
          <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
          @csrf
          @method('PUT')

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
              <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                     class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                     class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="mb-4">
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini (untuk verifikasi)</label>
            <input type="password" id="current_password" name="current_password"
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('current_password')
              <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
              <input type="password" id="password" name="password"
                     class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
              <input type="password" id="password_confirmation" name="password_confirmation"
                     class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
          </div>

          <div class="flex justify-end space-x-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
              Simpan Perubahan
            </button>
            <a href="{{ route('home') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
              Batal
            </a>
          </div>
        </form>
      </div>

      <!-- Bagian Logout -->
      <div class="border-t p-6 bg-gray-50">
        <h2 class="text-xl font-semibold mb-4">Keluar Akun</h2>
        <p class="mb-4 text-gray-600">Anda bisa keluar dari akun ini dan login kembali nanti.</p>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
            Keluar
          </button>
        </form>
      </div>
    </div>
  </div>
</body>
@endsection

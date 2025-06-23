<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MqttController;
use Salman\Mqtt\MqttClass\Mqtt;

// Redirect root URL ke login
Route::redirect('/', '/login');

// Rute untuk guest (belum login)
Route::middleware('guest')->group(function () {
    // Login
    // Pastikan route menggunakan array style
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Rute yang harus login
Route::middleware('auth')->group(function () {
    // Dashboard (halaman utama setelah login)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Nama route 'home'

    // Fitur lainnya
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // API
    Route::get('/check-latest-log', function () {
        $log = \App\Models\Log::latest()->first();
        return response()->json($log);
    });
    Route::post('/sensor-data', [LogController::class, 'store']);
});

Route::get('/mqtt/publish', [MqttController::class, 'publishTest']);
Route::get('/mqtt/subscribe', [MqttController::class, 'subscribe']);

Route::get('/test-publish', function() {
    $mqtt = new Mqtt();
    $mqtt->ConnectAndPublish(
        'ranindya/iot/sensor/data',
        json_encode([
            'suhu' => rand(20, 40),
            'kelembapan' => rand(30, 90),
            'gas' => rand(0, 2500),
        ])
    );

    return "Test message published";
});

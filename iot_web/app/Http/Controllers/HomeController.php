<?php

namespace App\Http\Controllers;

use App\Models\Log; // Import model Log jika diperlukan
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil data untuk status notifikasi
        $logs = Log::latest()->limit(1)->get(); // Mendapatkan log terbaru
        $query = Log::query();

        $logs = $query->latest()->take(10)->get()->reverse();

        // Ambil data untuk grafik (misalnya suhu dan gas)
        $labels = $logs->pluck('created_at')->map(fn($t) => $t->format('H:i'));
        $suhuData = $logs->pluck('suhu');
        $gasData = $logs->pluck('gas');

        $sensorData = [
            'labels' => $labels,
            'suhu' => $suhuData,
            'gas' => $gasData,
        ];

        return view('home', compact('logs', 'sensorData'));
    }
}

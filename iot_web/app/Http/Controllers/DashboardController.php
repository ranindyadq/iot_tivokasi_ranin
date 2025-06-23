<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $logs = $query->latest()->take(10)->get()->reverse();

        $labels = $logs->pluck('created_at')->map(fn($t) => $t->format('H:i'));
        $suhuData = $logs->pluck('suhu');
        $gasData = $logs->pluck('gas');

        $sensorData = [
            'labels' => $labels,
            'suhu' => $suhuData,
            'gas' => $gasData,
        ];

        return view('dashboard', compact('logs', 'sensorData'));
    }
}

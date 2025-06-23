<?php

namespace App\Http\Controllers;

use App\Models\Log; // Import model Log jika diperlukan
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        // Filter berdasarkan status jika ada
        $status = $request->query('status');
        $logs = Log::when($status, function($query) use ($status) {
            return $query->where('status', $status);
        })->orderBy('created_at', 'desc')->paginate(15); // Menampilkan 10 log terbaru

        return view('riwayat', compact('logs'));
    }
}

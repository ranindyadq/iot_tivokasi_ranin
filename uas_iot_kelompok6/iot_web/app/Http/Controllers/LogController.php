<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        // Menggunakan Log facade untuk menulis log
        Log::info('Sensor Data:', $data);
        return response()->json(['message' => 'Data received successfully'], 200);
    }
}


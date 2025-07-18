<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrendSentiment extends Controller
{
    public function get24HourSentimentTrend()
    {
        // Mendapatkan waktu saat ini dan 24 jam yang lalu
        $now = Carbon::now();
        $yesterday = $now->copy()->subHours(24);

        // Mengelompokkan data berdasarkan interval 4 jam
        $data = DB::table('news')
            ->select(
                DB::raw("CONCAT(FLOOR(EXTRACT(HOUR FROM created_at) / 4) * 4, ':00') AS jam"),
                'sentiment',
                DB::raw("COUNT(*) as total")
            )
            ->where('created_at', '>=', $yesterday)  // Filter untuk 24 jam terakhir
            ->groupBy(DB::raw("FLOOR(EXTRACT(HOUR FROM created_at) / 4)"), 'sentiment')
            ->orderBy(DB::raw("FLOOR(EXTRACT(HOUR FROM created_at) / 4)"))
            ->get();

        return response()->json($data);
    }
}

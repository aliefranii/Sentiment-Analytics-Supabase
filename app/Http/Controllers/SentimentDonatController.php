<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SentimentDonatController extends Controller
{
    public function getSentimentData(Request $request)
    {
        $range = $request->query('range', 'this_month');

        $query = DB::table('news')
            ->select('sentimen', DB::raw('count(*) as total'))
            ->whereNotNull('sentimen');

        // -- PERBAIKAN DI SINI --
        // Dibuat case-insensitive agar konsisten dan aman
        $query->where(DB::raw('LOWER(source)'), 'like', '%wajo%');

        // Menggunakan satu instance Carbon untuk konsistensi
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                // Menggunakan whereDate adalah cara paling andal untuk "hari ini"
                $query->whereDate('created_at', $now);
                break;

            case 'this_week':
                $query->whereBetween('created_at', [
                    $now->copy()->startOfWeek(),
                    $now->copy()->endOfWeek()
                ]);
                break;

            case 'this_month':
                $query->whereBetween('created_at', [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth()
                ]);
                break;

            case 'this_year':
                $query->whereYear('created_at', $now->year);
                break;

            default:
                $query->whereBetween('created_at', [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth()
                ]);
                break;
        }

        $data = $query->groupBy('sentimen')->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Belum ada data untuk range ini']);
        }

        return response()->json($data);
    }
}
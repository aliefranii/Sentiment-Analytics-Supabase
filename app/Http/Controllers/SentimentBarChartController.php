<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SentimentBarChartController extends Controller
{
    public function getBarData(Request $request)
    {
        $range = $request->query('range', 'this_month');

        $query = DB::table('news')
            ->select('source', 'sentiment', DB::raw('COUNT(*) as total'));

        // Filter berdasarkan range waktu
        switch ($range) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;

            case 'this_week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(), 
                    Carbon::now()->endOfWeek()
                ]);
                break;

            case 'this_month':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(), 
                    Carbon::now()->endOfMonth()
                ]);
                break;

            case 'this_year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;

            default:
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(), 
                    Carbon::now()->endOfMonth()
                ]);
                break;
        }

        // Group by source & sentiment
        $data = $query
            ->groupBy('source', 'sentiment')
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Belum ada data hari ini']);
        }

        return response()->json($data);
    }
}

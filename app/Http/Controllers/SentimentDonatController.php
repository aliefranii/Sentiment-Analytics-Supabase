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

        // Ambil waktu lokal Indonesia
        $now = Carbon::now('Asia/Jakarta');

        // Mulai query dan pastikan sentimen tidak null
        $query = DB::table('news')
            ->select('sentimen', DB::raw('count(*) as total'))
            ->whereNotNull('sentimen');

            $query->where('source', 'like', '%WAJO%');

        // Filter berdasarkan range waktu, dikonversi ke UTC
        switch ($range) {
            case 'today':
                $query->whereBetween('created_at', [
                    $now->copy()->startOfDay()->timezone('UTC'),
                    $now->copy()->endOfDay()->timezone('UTC')
                ]);
                break;

            case 'this_week':
                $query->whereBetween('created_at', [
                    $now->copy()->startOfWeek()->timezone('UTC'),
                    $now->copy()->endOfWeek()->timezone('UTC')
                ]);
                break;

            case 'this_month':
                $query->whereBetween('created_at', [
                    $now->copy()->startOfMonth()->timezone('UTC'),
                    $now->copy()->endOfMonth()->timezone('UTC')
                ]);
                break;

            case 'this_year':
                $query->whereBetween('created_at', [
                    $now->copy()->startOfYear()->timezone('UTC'),
                    $now->copy()->endOfYear()->timezone('UTC')
                ]);
                break;

            default:
                $query->whereBetween('created_at', [
                    $now->copy()->startOfMonth()->timezone('UTC'),
                    $now->copy()->endOfMonth()->timezone('UTC')
                ]);
                break;
        }

        // Group dan ambil hasil
        $data = $query->groupBy('sentimen')->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Belum ada data untuk range ini']);
        }

        return response()->json($data);
    }
}

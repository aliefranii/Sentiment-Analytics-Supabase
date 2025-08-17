<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrendSentiment extends Controller
{
    public function get24HourSentimentTrend()
    {
        // Ambil data hanya untuk hari ini
        $data = DB::table('news')
            ->select(
                DB::raw("CONCAT(FLOOR(EXTRACT(HOUR FROM created_at) / 4) * 4, ':00') AS jam"),
                'sentimen',
                'source',
                DB::raw("COUNT(*) as total")
            )
            // PERBAIKAN: Menambahkan () setelah Carbon::now
            ->whereDate('created_at', Carbon::now()->toDateString())
            ->whereNotNull('sentimen')
            ->where('source', 'like', '%WAJO%')
            ->groupBy(
                DB::raw("FLOOR(EXTRACT(HOUR FROM created_at) / 4)"),
                'sentimen',
                'source'
            )
            ->orderBy(DB::raw("FLOOR(EXTRACT(HOUR FROM created_at) / 4)"))
            ->get();

        // Format data agar konsisten dengan frontend (positive, neutral, negative)
        $formattedData = [];
        foreach ($data as $item) {
            // Menstandarkan sentimen
            $sentimen = strtolower($item->sentimen);
            if (!isset($formattedData[$item->jam])) {
                $formattedData[$item->jam] = [
                    'positif' => 0,
                    'netral'  => 0,
                    'negatif' => 0
                ];
            }

            // Tambahkan data sesuai sentimen
            if ($sentimen == 'positif') {
                $formattedData[$item->jam]['positif'] += $item->total;
            } elseif ($sentimen == 'negatif') {
                $formattedData[$item->jam]['negatif'] += $item->total;
            } elseif ($sentimen == 'netral') {
                $formattedData[$item->jam]['netral'] += $item->total;
            }
        }

        // Mengembalikan data dalam format yang dibutuhkan frontend
        $response = [];
        foreach ($formattedData as $jam => $sentimen) {
            $response[] = [
                'hour'     => $jam,
                'positive' => $sentimen['positif'],
                'neutral'  => $sentimen['netral'],
                'negative' => $sentimen['negatif']
            ];
        }

        return response()->json($response);
    }
}
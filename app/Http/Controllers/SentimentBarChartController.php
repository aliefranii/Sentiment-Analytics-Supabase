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

        // Mulai query
        $query = DB::table('news')
            ->select('source', 'sentimen', DB::raw('COUNT(*) as total'))
            ->whereNotNull('sentimen');

        $query->where('source', 'like', '%WAJO%'); // Filter berdasarkan kata 'wajo' dalam source

        switch ($range) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;

            case 'this_week':
                $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
                $endOfWeek = Carbon::now()->endOfWeek()->toDateString();
                \Log::info("Rentang waktu minggu ini: $startOfWeek - $endOfWeek");

                $query->whereBetween(DB::raw('DATE(created_at)'), [
                    $startOfWeek, // Mulai minggu ini
                    $endOfWeek    // Akhir minggu ini
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

        // Ambil semua data berdasarkan source dan sentimen
        $data = $query->groupBy('source', 'sentimen')->get();

        // Debugging: Periksa hasil data yang diambil
        \Log::info("Data yang diambil: ", $data->toArray());

        // Format data agar menjadi array dengan structure yang benar
        $formattedData = [];

        foreach ($data as $item) {
            $sentimen = strtolower($item->sentimen);  // Normalisasi sentimen menjadi lowercase
            if (!isset($formattedData[$item->source])) {
                // Inisialisasi array untuk source jika belum ada
                $formattedData[$item->source] = [
                    'positif' => 0,
                    'netral' => 0,
                    'negatif' => 0
                ];
            }

            // Menambahkan total count untuk setiap sentimen
            if ($sentimen == 'positif') {
                $formattedData[$item->source]['positif'] += $item->total;
            } elseif ($sentimen == 'negatif') {
                $formattedData[$item->source]['negatif'] += $item->total;
            } elseif ($sentimen == 'netral') {
                $formattedData[$item->source]['netral'] += $item->total;
            }
        }

        // Pastikan data dalam format array
        $formattedDataArray = array_map(function($sourceData, $source) {
            return [
                'source' => $source,
                'positif' => $sourceData['positif'],
                'netral' => $sourceData['netral'],
                'negatif' => $sourceData['negatif'],
            ];
        }, $formattedData, array_keys($formattedData));

        // Jika data kosong, kirimkan pesan yang sesuai
        if (empty($formattedDataArray)) {
            return response()->json(['message' => 'No data available for this range']);
        }

        // Kirimkan response dalam bentuk array
        return response()->json($formattedDataArray);
    }
}

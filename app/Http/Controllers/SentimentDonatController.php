<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SentimentDonatController extends Controller
{
    public function getSentimentData(Request $request)
    {
        $range = $request->query('range', 'this_month');

        $query = DB::table('news')
            ->select('sentiment', DB::raw('count(*) as total'));

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

        return response()->json($query->groupBy('sentiment')->get());
    }
}

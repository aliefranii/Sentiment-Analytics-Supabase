<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    { 

        // Rentang minggu ini
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString(); 
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();
        
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek()->toDateString();  
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek()->toDateString();
        
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth()->toDateString();  
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth()->toDateString();
        
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        
        $newsQuery = DB::table('news')->where('source', 'like', '%WAJO%');

        $totalNews = $newsQuery->count();

        $totalPositiveSentimentOverall = $newsQuery->clone()
            ->where('sentimen', 'Positif')
            ->count();

        // Bulan ini
        $totalPositiveSentimentThisMonth = $newsQuery->clone()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('sentimen', 'Positif')
            ->count();

        $totalNewsThisMonth = $newsQuery->clone()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $engagementRateThisMonth = $totalNewsThisMonth > 0
            ? ($totalPositiveSentimentThisMonth / $totalNews) * 100
            : 0;

        // Bulan lalu
        $totalPositiveSentimentLastMonth = $newsQuery->clone()
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->where('sentimen', 'Positif')
            ->count();

        $totalNewsLastMonth = $newsQuery->clone()
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $engagementRateLastMonth = $totalNewsLastMonth > 0
            ? ($totalPositiveSentimentLastMonth / $totalNews) * 100
            : 0;

        $percentageChangeEngagementRate = 0;
        if ($engagementRateLastMonth > 0) {
            $percentageChangeEngagementRate = (($engagementRateThisMonth - $engagementRateLastMonth) / $totalNews) * 100;
        } elseif ($engagementRateThisMonth > 0) {
            $percentageChangeEngagementRate = 100;
        }

        // Mingguan
        $totalNewsThisWeek = $newsQuery->clone()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->count();

        $totalNewsLastWeek = $newsQuery->clone()
            ->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        $percentageChangeNews = 0;
        if ($totalNewsLastWeek > 0) {
            $percentageChangeNews = (($totalNewsThisWeek - $totalNewsLastWeek) / $totalNewsLastWeek) * 100;
        } elseif ($totalNewsThisWeek > 0) {
            $percentageChangeNews = 100;
        }

        $feeds = DB::table('news')
            ->select('id', 'title', 'desc', 'source', 'sentimen', 'created_at')
            ->where('source', 'like', '%WAJO%')  // Menambahkan kondisi untuk mencari "WAJO" di kolom 'source'
            ->orderBy('created_at', 'desc')  // Mengurutkan berdasarkan tanggal terbaru
            ->limit(10)  // Mengambil 10 entri pertama
            ->get();

        View::share([
            'totalNews' => $totalNews,
            'totalPositiveSentimentOverall' => $totalPositiveSentimentOverall,
            'percentageChangeEngagementRate' => $percentageChangeEngagementRate,
            'engagementRateThisMonth' => $engagementRateThisMonth,
            'feeds' => $feeds,
            'percentageChangeNews' => $percentageChangeNews
        ]);
    }
}

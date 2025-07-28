<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /* ---------- Rentang waktu ---------- */
        $startOfWeek      = Carbon::now()->startOfWeek();
        $endOfWeek        = Carbon::now()->endOfWeek();
        $startOfLastWeek  = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek    = Carbon::now()->subWeek()->endOfWeek();

        $startOfMonth     = Carbon::now()->startOfMonth();
        $endOfMonth       = Carbon::now()->endOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth   = Carbon::now()->subMonth()->endOfMonth();

        /* ---------- Base query ---------- */
        $newsQuery = DB::table('news')
            ->whereRaw('source ilike ?', ['%WAJO%']);

        /* ---------- Statistik umum ---------- */
        $totalNews                     = $newsQuery->count();
        $totalPositiveSentimentOverall = $newsQuery->clone()
            ->where('sentimen', 'Positif')
            ->count();

        /* ---------- Bulan ini ---------- */
        $totalPositiveSentimentThisMonth = $newsQuery->clone()
            ->whereBetween('created_at', [
                $startOfMonth->toDateTimeString(),
                $endOfMonth->toDateTimeString()
            ])
            ->where('sentimen', 'Positif')
            ->count();

        $totalNewsThisMonth = $newsQuery->clone()
            ->whereBetween('created_at', [
                $startOfMonth->toDateTimeString(),
                $endOfMonth->toDateTimeString()
            ])
            ->count();

        $engagementRateThisMonth = $totalNews > 0
            ? ($totalPositiveSentimentThisMonth / $totalNews) * 100
            : 0;

        /* ---------- Bulan lalu ---------- */
        $totalPositiveSentimentLastMonth = $newsQuery->clone()
            ->whereBetween('created_at', [
                $startOfLastMonth->toDateTimeString(),
                $endOfLastMonth->toDateTimeString()
            ])
            ->where('sentimen', 'Positif')
            ->count();

        $totalNewsLastMonth = $newsQuery->clone()
            ->whereBetween('created_at', [
                $startOfLastMonth->toDateTimeString(),
                $endOfLastMonth->toDateTimeString()
            ])
            ->count();

        $engagementRateLastMonth = $totalNews > 0
            ? ($totalPositiveSentimentLastMonth / $totalNews) * 100
            : 0;

        /* ---------- Persentase perubahan engagement ---------- */
        $percentageChangeEngagementRate = 0;
        if ($engagementRateLastMonth > 0) {
            $percentageChangeEngagementRate =
                (($engagementRateThisMonth - $engagementRateLastMonth) / $engagementRateLastMonth) * 100;
        } elseif ($engagementRateThisMonth > 0) {
            $percentageChangeEngagementRate = 100;
        }

        /* ---------- Mingguan ---------- */
        $totalNewsThisWeek = $newsQuery->clone()
            ->whereBetween('created_at', [
                $startOfWeek->toDateTimeString(),
                $endOfWeek->toDateTimeString()
            ])
            ->count();

        $totalNewsLastWeek = $newsQuery->clone()
            ->whereBetween('created_at', [
                $startOfLastWeek->toDateTimeString(),
                $endOfLastWeek->toDateTimeString()
            ])
            ->count();

        $percentageChangeNews = 0;
        if ($totalNewsLastWeek > 0) {
            $percentageChangeNews =
                (($totalNewsThisWeek - $totalNewsLastWeek) / $totalNewsLastWeek) * 100;
        } elseif ($totalNewsThisWeek > 0) {
            $percentageChangeNews = 100;
        }

        /* ---------- Data feeds ---------- */
        $feeds = DB::table('news')
            ->select('id', 'title', 'desc', 'source', 'sentimen', 'created_at')
            ->whereRaw('source ilike ?', ['%WAJO%'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        /* ---------- Share ke semua view ---------- */
        View::share([
            'totalNews'                     => $totalNews,
            'totalPositiveSentimentOverall' => $totalPositiveSentimentOverall,
            'percentageChangeEngagementRate'=> $percentageChangeEngagementRate,
            'engagementRateThisMonth'       => $engagementRateThisMonth,
            'feeds'                         => $feeds,
            'percentageChangeNews'          => $percentageChangeNews,
        ]);
    }
}
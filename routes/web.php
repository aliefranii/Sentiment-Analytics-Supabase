<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentimentDonatController;
use App\Http\Controllers\SentimentBarChartController;
use App\Http\Controllers\TrendSentiment;

// Halaman Overview (menampilkan blade)
Route::get('/overview', function () {
    return view('overview');
});

// Data untuk Doughnut Chart dari tabel news
Route::get('/overview/sentiment-data', [SentimentDonatController::class, 'getSentimentData']);
Route::get('/overview/sentiment-bar-data', [SentimentBarChartController::class, 'getBarData']);
Route::get('/overview/chart-24h-sentiment', [TrendSentiment::class, 'get24HourSentimentTrend']);

// Halaman Sources
Route::get('/sources', function () {
    return view('sources');
});

// Halaman Trends
Route::get('/trends', function () {
    return view('trends');
});

// Halaman Mentions
Route::get('/mentions', function () {
    return view('mentions');
});

// Redirect root ke /overview
Route::get('/', function () {
    return redirect('/overview');
});

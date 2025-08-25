<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentimentDonatController;
use App\Http\Controllers\SentimentBarChartController;
use App\Http\Controllers\TrendSentiment;
use App\Http\Controllers\RecommendationsController;

Route::get('/overview', [RecommendationsController::class, 'recommendations'])->name('overview');

Route::get('/overview/sentiment-data', [SentimentDonatController::class, 'getSentimentData']);
Route::get('/api/sentiment-bar-data', [SentimentBarChartController::class, 'getBarData']);
Route::get('/overview/chart-24h-sentiment', [TrendSentiment::class, 'get24HourSentimentTrend']);

Route::get('/sources', function () {
    return view('sources');
});

Route::get('/trends', function () {
    return view('trends');
});

Route::get('/mentions', function () {
    return view('mentions');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/', function () {
    return redirect('/login');
});

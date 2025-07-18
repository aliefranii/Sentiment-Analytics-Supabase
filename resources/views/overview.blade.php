@extends('layouts.app')

@section('content')
    <div class="max-w-screen my-4 space-y-4 rtl:space-y-reverse">
        <x-sentiment-distribution></x-sentiment-distribution>
        <x-trend-sentiment></x-trend-sentiment>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/sentiment.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

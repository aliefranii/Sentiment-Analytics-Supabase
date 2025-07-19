@extends('layouts.app')

@section('content')
    <div class="flex flex-col rtl:space-y-reverse">
        <!-- Wrapper for Doughnut and Bar Chart -->
        <div class="flex flex-col lg:flex-row space-y-4 lg:space-x-4 w-full">
            <!-- Doughnut Chart -->
            <div class="flex-1">
                <x-doughnut-chart></x-doughnut-chart>
            </div>
            <!-- Bar Chart -->
            <div class="flex-1">
                <x-bar-chart></x-bar-chart>
            </div>
        </div>

        <!-- Line Chart Section -->
        <div class="flex flex-col lg:flex-row space-y-4 lg:space-x-4 w-full">
            <div class="flex-1">
                <x-line-chart></x-line-chart>
            </div>
        </div>

        <!-- Container Section for small screens -->
        <div class="flex justify-center sm:w-full lg:w-[500px]">
            <x-container></x-container>
        </div>
    </div>
@endsection

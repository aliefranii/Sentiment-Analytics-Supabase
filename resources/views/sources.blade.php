@extends ('layouts.app')

@section('content')
    <div class="flex flex-col space-y-4 rtl:space-y-reverse">
        <!-- Wrapper for Doughnut and Bar Chart -->
        <div class="flex flex-col lg:flex-row space-y-4 lg:space-x-4 w-full">
            <!-- Doughnut Chart -->
            <div class="flex-1">
                <x-bar-chart></x-bar-chart>
            </div>
            <!-- Bar Chart -->
            <div class="flex-1">
                <x-container></x-container>
            </div>
        </div>
    </div>
@endsection

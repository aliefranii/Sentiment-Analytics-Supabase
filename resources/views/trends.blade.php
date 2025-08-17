@extends('layouts.app')

@section('content')
    <div class="flex flex-col space-y-4 rtl:space-y-reverse">
        <div class="w-full">
            <x-line-chart></x-line-chart>
        </div>
        <div class="h-4"></div>
        <div class="flex flex-col lg:flex-row space-y-4 lg:space-x-4 w-full">
            <div class="flex-1">
                <x-container :feeds="$feeds" />
            </div>
            <div class="flex sm:w-full lg:w-[450px]">
                <x-feeds :feeds="$feeds" />
            </div>
        </div>
    </div>
@endsection

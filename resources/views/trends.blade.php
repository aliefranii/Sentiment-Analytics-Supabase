@extends('layouts.app')

@section('content')
    <div class="">
        <div class="flex flex-col space-y-4 rtl:space-y-reverse">
            <div class="w-full">
                <x-bar-chart></x-bar-chart>
            </div>

            <div class="flex flex-col lg:flex-row space-y-4 lg:space-x-4 w-full">
                <div class="flex-1">
                    <x-container></x-container>
                </div>
                <div class="flex sm:w-full lg:w-[450px]">
                    <x-container></x-container>
                </div>
            </div>
        </div>
    </div>
@endsection

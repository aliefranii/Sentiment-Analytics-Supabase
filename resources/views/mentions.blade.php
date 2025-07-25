@extends('layouts.app')

@section('content')
    <div class="flex flex-col space-y-4 rtl:space-y-reverse">
        <div class="flex flex-col lg:flex-row space-y-4 lg:space-x-4 w-full">
            <div class="flex-1 w-full      ">
                <x-container :feeds="$feeds" />
            </div>
        </div>
    </div>
@endsection

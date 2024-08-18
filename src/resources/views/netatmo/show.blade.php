{{-- src/resources/views/memoryvehicle/show.blade.php --}}
@extends('netatmoweather::layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
            <h2 class="text-xl font-bold mb-4">WS</h2>

            <div class="mb-4">
                <strong>Name:</strong> {{ $weatherStation->station_name }}

                @if($data)
                    {{ print_r($data) }}
                @endif

            </div>
        </div>
@endsection

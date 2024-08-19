{{-- src/resources/views/memoryvehicle/show.blade.php --}}
@extends('netatmoweather::layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
            <h2 class="text-xl font-bold mb-4">{{ $weatherStation->station_name }}</h2>

            <div class="mb-4">
                @foreach($weatherStation->modules as $module)
                    <div class="mb-4">
                        <span class="font-bold">Module:</span> {{ $module->module_name }}
                        <small>{{ $module->module_id }}</small>
                        <div class="mb-4">
                            {{--                                <strong>Measure:</strong> {{ $reading->type }}--}}
                            {{--                            <strong>Value:</strong> {{ $module->latestReading }}--}}
                            @foreach($module->latestReading['dashboard_data'] as $key => $reading)
                                {{ $key }}: {{ $reading }} <br>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
@endsection

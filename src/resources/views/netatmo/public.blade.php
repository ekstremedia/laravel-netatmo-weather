{{-- src/resources/views/netatmo/public.blade.php --}}
@extends('netatmoweather::layouts.public')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header - Simplified -->
        <div class="flex items-center justify-center mb-8">
            <div class="text-center">
                <div class="flex items-center justify-center space-x-4 mb-3">
                    <div class="bg-gradient-to-br from-netatmo-purple via-purple-600 to-netatmo-deep p-3 rounded-2xl shadow-lg shadow-purple-900/50 ring-2 ring-purple-500/20">
                        <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}"
                             alt="Weather Station"
                             class="w-10 h-10">
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-white">{{ $weatherStation->station_name }}</h1>
                <p class="text-sm text-purple-300/80 mt-2">
                    <i class="fa-solid fa-signal mr-1"></i>
                    {{ $weatherStation->modules->count() }} {{ $weatherStation->modules->count() === 1 ? 'module' : 'modules' }} connected
                </p>
                @if($weatherStation->modules->first()?->dashboard_data)
                    <p class="text-xs text-purple-400/60 mt-1">
                        <i class="fa-solid fa-clock mr-1"></i>
                        Last updated: @datetime($weatherStation->modules->first()->dashboard_data['time_utc'])
                    </p>
                @endif
            </div>
        </div>

        <!-- Modules Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($weatherStation->modules->where('is_active', true) as $module)
                @if($module->type === 'NAMain')
                    <div class="col-span-1 lg:col-span-2">
                        @include('netatmoweather::netatmo.widgets.stationData', ['module' => $module])
                    </div>
                @else
                    <div class="col-span-1">
                        @include('netatmoweather::netatmo.widgets.stationData', ['module' => $module])
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Footer with attribution -->
        <div class="mt-12 text-center">
            <p class="text-xs text-purple-400/50">
                <i class="fa-solid fa-cloud mr-1"></i>
                Powered by Netatmo Weather Station
            </p>
        </div>
    </div>
@endsection

{{-- src/resources/views/netatmo/show.blade.php --}}
@extends('netatmoweather::layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-netatmo-purple via-purple-600 to-netatmo-deep p-3 rounded-2xl shadow-lg shadow-purple-900/50 ring-2 ring-purple-500/20">
                    <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}"
                         alt="Weather Station"
                         class="w-10 h-10 brightness-0 invert">
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $weatherStation->station_name }}</h1>
                    <p class="text-sm text-purple-300/80 mt-1">
                        <i class="fa-solid fa-signal mr-1"></i>
                        {{ $weatherStation->modules->count() }} {{ $weatherStation->modules->count() === 1 ? 'module' : 'modules' }} connected
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('netatmo.edit', $weatherStation) }}">
                    <button
                        class="inline-flex items-center space-x-2 px-6 py-3 bg-dark-surface/60 hover:bg-dark-surface border border-dark-border/50 text-purple-200 font-medium rounded-xl transition-all duration-200">
                        <i class="fas fa-edit"></i>
                        <span>Edit Station</span>
                    </button>
                </a>
                <a href="{{ route('netatmo.authenticate', $weatherStation) }}">
                    <button
                        class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-netatmo-purple via-purple-600 to-netatmo-deep hover:from-netatmo-deep hover:to-purple-900 text-white font-semibold rounded-xl shadow-lg shadow-purple-900/50 hover:shadow-xl hover:shadow-purple-800/50 transform hover:-translate-y-0.5 transition-all duration-200 ring-2 ring-purple-500/20">
                        <i class="fas fa-sync-alt"></i>
                        <span>Refresh Data</span>
                    </button>
                </a>
            </div>
        </div>

        <!-- Modules Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($weatherStation->modules as $module)
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
    </div>
@endsection

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
                         class="w-10 h-10">
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $weatherStation->station_name }}</h1>
                    <p class="text-sm text-purple-300/80 mt-1">
                        <i class="fa-solid fa-signal mr-1"></i>
                        {{ $weatherStation->modules->where('is_active', true)->count() }} {{ $weatherStation->modules->where('is_active', true)->count() === 1 ? 'module' : 'modules' }}
                        connected
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

        <!-- Archived Modules Section -->
        @if($weatherStation->modules->where('is_active', false)->count() > 0)
            <div class="mt-8 bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-xl border border-dark-border/50 overflow-hidden"
                 data-expandable>
                <div class="px-6 py-4 border-b border-dark-border/50 bg-gradient-to-r from-dark-surface/60 to-orange-900/10">
                    <div class="flex items-center justify-between cursor-pointer" data-expand-toggle>
                        <div class="flex items-center space-x-3">
                            <div class="bg-gradient-to-br from-orange-500/20 to-orange-600/20 p-2.5 rounded-lg border border-orange-500/30">
                                <i class="fas fa-archive text-orange-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Archived Modules</h3>
                                <p class="text-xs text-orange-300/70">
                                    {{ $weatherStation->modules->where('is_active', false)->count() }}
                                    {{ $weatherStation->modules->where('is_active', false)->count() === 1 ? 'module' : 'modules' }}
                                    no longer detected
                                </p>
                            </div>
                        </div>
                        <div class="transition-transform duration-200" data-expand-icon>
                            <i class="fas fa-chevron-down text-purple-400"></i>
                        </div>
                    </div>
                </div>

                <div data-expand-target
                     class="hidden px-6 py-4">
                    <div class="space-y-3">
                        @foreach($weatherStation->modules->where('is_active', false) as $module)
                            <div class="bg-dark-surface/40 border border-orange-900/30 rounded-xl p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-4 flex-1">
                                        <div class="bg-orange-500/10 p-3 rounded-lg border border-orange-500/20">
                                            <i class="fas fa-{{ match($module->type) {
                                                'NAMain' => 'home',
                                                'NAModule1' => 'cloud-sun',
                                                'NAModule2' => 'wind',
                                                'NAModule3' => 'cloud-rain',
                                                'NAModule4' => 'door-open',
                                                default => 'cube'
                                            } }} text-orange-400"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-white font-semibold">
                                                {{ $module->module_name }}
                                                @if($module->module_name === 'Unknown Module')
                                                    <span class="text-orange-400/60 font-normal text-sm">
                                                        ({{ match($module->type) {
                                                            'NAMain' => 'Indoor Module',
                                                            'NAModule1' => 'Outdoor Module',
                                                            'NAModule2' => 'Wind Gauge',
                                                            'NAModule3' => 'Rain Gauge',
                                                            'NAModule4' => 'Additional Indoor',
                                                            default => $module->type
                                                        } }})
                                                    </span>
                                                @endif
                                            </h4>
                                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                                <span class="text-xs text-orange-300/60">
                                                    Type: {{ $module->type }}
                                                </span>
                                                @if($module->last_seen)
                                                    <span class="text-xs text-orange-300/60">
                                                        Last seen: {{ \Carbon\Carbon::createFromTimestamp($module->last_seen)->diffForHumans() }}
                                                    </span>
                                                @endif
                                                @if($module->battery_percent)
                                                    <span class="text-xs text-orange-300/60">
                                                        Battery: {{ $module->battery_percent }}%
                                                    </span>
                                                @endif
                                                @if($module->rf_status)
                                                    <span class="text-xs text-orange-300/60">
                                                        RF: {{ $module->rf_status }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if(is_array($module->data_type) && count($module->data_type) > 0)
                                                <div class="mt-2 flex items-center gap-2">
                                                    <span class="text-xs text-blue-400/70">
                                                        <i class="fas fa-database mr-1"></i>Data Types:
                                                    </span>
                                                    @foreach($module->data_type as $dataType)
                                                        <span class="inline-flex items-center px-2 py-0.5 bg-blue-500/10 border border-blue-500/30 rounded text-xs text-blue-300">
                                                            {{ $dataType }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <p class="text-xs text-orange-400/50 mt-2">
                                                Module ID: {{ $module->module_id }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                        <form method="POST"
                                              action="{{ route('netatmo.modules.activate', [$weatherStation, $module]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center space-x-2 px-4 py-2 bg-green-900/20 hover:bg-green-900/40 border border-green-500/30 hover:border-green-500/50 text-green-400 hover:text-green-300 text-sm font-medium rounded-lg transition-all duration-200">
                                                <i class="fas fa-check-circle"></i>
                                                <span>Reactivate</span>
                                            </button>
                                        </form>
                                        <form method="POST"
                                              action="{{ route('netatmo.modules.destroy', [$weatherStation, $module]) }}"
                                              data-confirm-submit="Are you sure you want to permanently delete this module?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center space-x-2 px-4 py-2 bg-red-900/20 hover:bg-red-900/40 border border-red-500/30 hover:border-red-500/50 text-red-400 hover:text-red-300 text-sm font-medium rounded-lg transition-all duration-200">
                                                <i class="fas fa-trash"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 bg-orange-900/20 border border-orange-700/30 rounded-xl p-3">
                        <p class="text-xs text-orange-300/70">
                            <i class="fas fa-info-circle mr-1"></i>
                            These modules are no longer detected by the Netatmo API. They may have been removed, lost
                            connection, or have dead batteries.
                            You can safely delete them if they're no longer needed.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection

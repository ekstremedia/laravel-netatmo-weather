{{-- resources/views/netatmo/index.blade.php --}}

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
                    <h1 class="text-3xl font-bold text-white">
                        {{ trans('netatmoweather::messages.weatherstation.weatherstations') }}
                    </h1>
                    <p class="text-sm text-purple-300/80 mt-1">
                        <i class="fa-solid fa-database mr-1"></i>
                        {{ $weatherStations->count() }} {{ $weatherStations->count() === 1 ? 'station' : 'stations' }} configured
                    </p>
                </div>
            </div>

            <a href="{{ route('netatmo.create') }}">
                <button
                    class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-netatmo-purple via-purple-600 to-netatmo-deep hover:from-netatmo-deep hover:to-purple-900 text-white font-semibold rounded-xl shadow-lg shadow-purple-900/50 hover:shadow-xl hover:shadow-purple-800/50 transform hover:-translate-y-0.5 transition-all duration-200 ring-2 ring-purple-500/20">
                    <i class="fas fa-plus-circle"></i>
                    <span>{{ trans('netatmoweather::messages.weatherstation.add') }}</span>
                </button>
            </a>
        </div>

        <!-- Weather Stations List -->
        @if($weatherStations->count())
            <div class="grid grid-cols-1 gap-6">
                @foreach($weatherStations as $weatherstation)
                    <div class="bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-2xl shadow-purple-900/20 hover:shadow-purple-800/30 transition-all duration-300 overflow-hidden border border-dark-border/50 hover:border-purple-500/50"
                         x-data="{ showConfirm: false }">
                        <!-- Station Card Header -->
                        <div class="bg-gradient-to-r from-dark-surface/60 to-purple-900/20 px-6 py-4 border-b border-dark-border/50">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-dark-elevated/80 p-3 rounded-xl shadow-sm border border-dark-border/30">
                                        <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}"
                                             alt="Station"
                                             class="w-8 h-8 brightness-150">
                                    </div>
                                    <div>
                                        <a href="{{ route('netatmo.show', $weatherstation) }}"
                                           class="text-xl font-bold text-white hover:text-purple-300 transition-colors">
                                            {{ $weatherstation->station_name }}
                                        </a>
                                        <div class="flex items-center mt-1">
                                            @if($weatherstation->token && $weatherstation->token->hasValidToken())
                                                <span class="inline-flex items-center space-x-1 text-sm text-green-400">
                                                    <i class="fa fa-check-circle"></i>
                                                    <span class="font-medium">Connected & Ready</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center space-x-1 text-sm text-amber-400">
                                                    <i class="fa fa-exclamation-circle animate-pulse"></i>
                                                    <span class="font-medium">Authentication Required</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Station Card Body -->
                        <div class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                @if($weatherstation->token && $weatherstation->token->hasValidToken())
                                    <a href="{{ route('netatmo.show', $weatherstation) }}"
                                       class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-netatmo-purple to-netatmo-deep hover:from-netatmo-deep hover:to-purple-900 text-white font-medium rounded-lg transition-all duration-200 shadow-lg shadow-purple-900/30">
                                        <i class="fas fa-chart-line"></i>
                                        <span>View Data</span>
                                    </a>
                                @else
                                    <a href="{{ route('netatmo.authenticate', $weatherstation) }}"
                                       class="inline-flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-lg shadow-amber-900/30 animate-pulse">
                                        <i class="fas fa-key"></i>
                                        <span>Authenticate Now</span>
                                    </a>
                                @endif

                                <a href="{{ route('netatmo.edit', $weatherstation) }}"
                                   class="inline-flex items-center space-x-2 px-4 py-2 bg-dark-surface/60 hover:bg-dark-surface border border-dark-border/50 text-purple-200 font-medium rounded-lg transition-all duration-200">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>

                                <button @click="showConfirm = true"
                                        class="inline-flex items-center space-x-2 px-4 py-2 bg-red-900/20 hover:bg-red-900/30 text-red-400 border border-red-800/30 font-medium rounded-lg transition-all duration-200 ml-auto">
                                    <i class="fas fa-trash-alt"></i>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>

                        <!-- Confirmation Modal -->
                        <div x-show="showConfirm"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 flex items-center justify-center bg-black/70 backdrop-blur-sm z-50 p-4">
                            <div @click.away="showConfirm = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="bg-dark-elevated border border-dark-border/50 rounded-2xl shadow-2xl max-w-md w-full p-6">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="bg-red-900/30 p-3 rounded-full border border-red-800/30">
                                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold text-white">Confirm Deletion</h2>
                                </div>
                                <p class="text-purple-200 mb-6">
                                    Are you sure you want to delete
                                    <strong class="text-white">{{ $weatherstation->station_name }}</strong>?
                                    This action cannot be undone.
                                </p>
                                <div class="flex justify-end space-x-3">
                                    <button @click="showConfirm = false"
                                            class="px-5 py-2.5 bg-dark-surface hover:bg-dark-surface/60 border border-dark-border/50 text-purple-200 font-medium rounded-lg transition-colors">
                                        Cancel
                                    </button>
                                    <button @click="$refs.deleteForm{{ $weatherstation->id }}.submit()"
                                            class="px-5 py-2.5 bg-red-900/40 hover:bg-red-900/60 border border-red-800/50 text-red-300 font-medium rounded-lg transition-colors shadow-sm hover:shadow-red-900/20">
                                        Yes, Delete
                                    </button>
                                </div>

                                <!-- Hidden form for delete action -->
                                <form x-ref="deleteForm{{ $weatherstation->id }}"
                                      method="POST"
                                      action="{{ route('netatmo.destroy', $weatherstation) }}"
                                      class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-2xl shadow-purple-900/20 border border-dark-border/50 p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="bg-gradient-to-br from-purple-900/40 via-purple-800/30 to-netatmo-deep/20 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 border border-purple-500/20 shadow-lg shadow-purple-900/30">
                        <i class="fas fa-cloud-sun text-4xl text-purple-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">No Weather Stations</h3>
                    <p class="text-purple-200/80 mb-6">
                        Get started by adding your first Netatmo weather station to start tracking weather data.
                    </p>
                    <a href="{{ route('netatmo.create') }}">
                        <button
                            class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-netatmo-purple via-purple-600 to-netatmo-deep hover:from-netatmo-deep hover:to-purple-900 text-white font-semibold rounded-xl shadow-lg shadow-purple-900/50 hover:shadow-xl hover:shadow-purple-800/50 transform hover:-translate-y-0.5 transition-all duration-200 ring-2 ring-purple-500/20">
                            <i class="fas fa-plus-circle"></i>
                            <span>{{ trans('netatmoweather::messages.weatherstation.add') }}</span>
                        </button>
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

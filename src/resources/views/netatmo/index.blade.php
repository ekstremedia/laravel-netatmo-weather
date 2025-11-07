{{-- resources/views/netatmo/index.blade.php --}}

@extends('netatmoweather::layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-2xl shadow-lg">
                    <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}"
                         alt="Weather Station"
                         class="w-10 h-10 brightness-0 invert">
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">
                        {{ trans('netatmoweather::messages.weatherstation.weatherstations') }}
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        <i class="fa-solid fa-database mr-1"></i>
                        {{ $weatherStations->count() }} {{ $weatherStations->count() === 1 ? 'station' : 'stations' }} configured
                    </p>
                </div>
            </div>

            <a href="{{ route('netatmo.create') }}">
                <button
                    class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-netatmo-blue to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-plus-circle"></i>
                    <span>{{ trans('netatmoweather::messages.weatherstation.add') }}</span>
                </button>
            </a>
        </div>

        <!-- Weather Stations List -->
        @if($weatherStations->count())
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($weatherStations as $weatherstation)
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-200/50"
                         x-data="{ showConfirm: false }">
                        <!-- Station Card Header -->
                        <div class="bg-gradient-to-r from-slate-50 to-blue-50 px-6 py-4 border-b border-slate-200/50">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-white p-3 rounded-xl shadow-sm">
                                        <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}"
                                             alt="Station"
                                             class="w-8 h-8">
                                    </div>
                                    <div>
                                        <a href="{{ route('netatmo.show', $weatherstation) }}"
                                           class="text-xl font-bold text-slate-800 hover:text-netatmo-blue transition-colors">
                                            {{ $weatherstation->station_name }}
                                        </a>
                                        <div class="flex items-center mt-1">
                                            @if($weatherstation->token && $weatherstation->token->hasValidToken())
                                                <span class="inline-flex items-center space-x-1 text-sm text-green-600">
                                                    <i class="fa fa-check-circle"></i>
                                                    <span class="font-medium">Authenticated</span>
                                                </span>
                                            @else
                                                <a href="{{ route('netatmo.authenticate', $weatherstation) }}"
                                                   class="inline-flex items-center space-x-1 px-3 py-1 bg-amber-100 hover:bg-amber-200 text-amber-700 text-sm font-medium rounded-lg transition-colors">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                    <span>Authenticate Required</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Station Card Body -->
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex space-x-2">
                                    <a href="{{ route('netatmo.show', $weatherstation) }}"
                                       class="inline-flex items-center space-x-2 px-4 py-2 bg-netatmo-blue hover:bg-blue-600 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow">
                                        <i class="fas fa-eye"></i>
                                        <span>View</span>
                                    </a>

                                    <a href="{{ route('netatmo.edit', $weatherstation) }}"
                                       class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-all duration-200">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </a>
                                </div>

                                <button @click="showConfirm = true"
                                        class="inline-flex items-center space-x-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-lg transition-all duration-200">
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
                             class="fixed inset-0 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm z-50 p-4">
                            <div @click.away="showConfirm = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                                <div class="flex items-center space-x-3 mb-4">
                                    <div class="bg-red-100 p-3 rounded-full">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                                    </div>
                                    <h2 class="text-2xl font-bold text-slate-800">Confirm Deletion</h2>
                                </div>
                                <p class="text-slate-600 mb-6">
                                    Are you sure you want to delete
                                    <strong class="text-slate-800">{{ $weatherstation->station_name }}</strong>?
                                    This action cannot be undone.
                                </p>
                                <div class="flex justify-end space-x-3">
                                    <button @click="showConfirm = false"
                                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-colors">
                                        Cancel
                                    </button>
                                    <button @click="$refs.deleteForm{{ $weatherstation->id }}.submit()"
                                            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow">
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
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-slate-200/50 p-12 text-center">
                <div class="max-w-md mx-auto">
                    <div class="bg-gradient-to-br from-blue-100 to-indigo-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-cloud-sun text-4xl text-netatmo-blue"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-2">No Weather Stations</h3>
                    <p class="text-slate-600 mb-6">
                        Get started by adding your first Netatmo weather station to start tracking weather data.
                    </p>
                    <a href="{{ route('netatmo.create') }}">
                        <button
                            class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-netatmo-blue to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-plus-circle"></i>
                            <span>{{ trans('netatmoweather::messages.weatherstation.add') }}</span>
                        </button>
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

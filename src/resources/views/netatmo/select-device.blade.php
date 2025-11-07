{{-- src/resources/views/netatmo/select-device.blade.php --}}
@extends('netatmoweather::layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="flex items-center space-x-4 mb-8">
            <div class="bg-gradient-to-br from-netatmo-purple via-purple-600 to-netatmo-deep p-3 rounded-2xl shadow-lg shadow-purple-900/50 ring-2 ring-purple-500/20">
                <i class="fas fa-broadcast-tower text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-white">Select Weather Station Device</h1>
                <p class="text-sm text-purple-300/80 mt-1">
                    Choose which Netatmo device this configuration should use
                </p>
            </div>
        </div>

        <!-- Info Box -->
        <div class="max-w-3xl mx-auto mb-8">
            <div class="bg-blue-900/20 border border-blue-700/30 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-info-circle text-blue-400 text-lg mt-1"></i>
                    <div>
                        <h4 class="text-blue-300 font-semibold mb-1">Multiple Devices Found</h4>
                        <p class="text-blue-200/70 text-sm">
                            Your Netatmo account has access to multiple weather stations. Please select which one this configuration ("{{ $weatherStation->station_name }}") should display data from.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Selection -->
        <div class="max-w-3xl mx-auto">
            <form method="POST" action="{{ route('netatmo.set-device', $weatherStation) }}">
                @csrf

                <div class="space-y-4">
                    @foreach($devices as $device)
                        <label class="block cursor-pointer">
                            <input type="radio"
                                   name="device_id"
                                   value="{{ $device['device_id'] }}"
                                   class="hidden peer"
                                   {{ $loop->first ? 'checked' : '' }}
                                   required>

                            <div class="bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-xl border border-dark-border/50 p-6 transition-all duration-200 peer-checked:border-netatmo-purple peer-checked:ring-2 peer-checked:ring-netatmo-purple/50 hover:border-purple-500/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-gradient-to-br from-purple-500/20 to-purple-600/20 p-3 rounded-lg border border-purple-500/30">
                                            <i class="fas fa-home text-purple-400 text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-white">{{ $device['station_name'] }}</h3>
                                            <p class="text-sm text-purple-300/70 mt-1">
                                                <i class="fas fa-cube mr-1"></i>
                                                {{ $device['module_count'] }} {{ $device['module_count'] === 1 ? 'module' : 'modules' }}
                                            </p>
                                            <p class="text-xs text-purple-400/50 mt-1">
                                                Device ID: {{ $device['device_id'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="peer-checked:block hidden">
                                        <div class="bg-netatmo-purple rounded-full p-2">
                                            <i class="fas fa-check text-white"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                @error('device_id')
                    <p class="text-red-400 text-sm mt-2 flex items-center space-x-1">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror

                <div class="mt-8 flex items-center justify-end space-x-3">
                    <a href="{{ route('netatmo.index') }}"
                       class="inline-flex items-center space-x-2 px-6 py-3 bg-dark-surface/60 hover:bg-dark-surface border border-dark-border/50 text-purple-200 font-medium rounded-xl transition-all duration-200">
                        <i class="fas fa-times"></i>
                        <span>Cancel</span>
                    </a>
                    <button type="submit"
                            class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-netatmo-purple via-purple-600 to-netatmo-deep hover:from-netatmo-deep hover:to-purple-900 text-white font-semibold rounded-xl shadow-lg shadow-purple-900/50 hover:shadow-xl hover:shadow-purple-800/50 transform hover:-translate-y-0.5 transition-all duration-200 ring-2 ring-purple-500/20">
                        <i class="fas fa-check"></i>
                        <span>Confirm Selection</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

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
                        {{ $weatherStation->modules->where('is_active', true)->count() }} {{ $weatherStation->modules->where('is_active', true)->count() === 1 ? 'module' : 'modules' }} connected
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
                <a href="{{ route('netatmo.select-device', $weatherStation) }}">
                    <button
                        class="inline-flex items-center space-x-2 px-6 py-3 bg-dark-surface/60 hover:bg-dark-surface border border-dark-border/50 text-purple-200 font-medium rounded-xl transition-all duration-200"
                        title="Change which Netatmo device this configuration uses">
                        <i class="fas fa-broadcast-tower"></i>
                        <span>Change Device</span>
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

        <!-- Public Access Section -->
        <div class="mb-8 bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-xl border border-dark-border/50 overflow-hidden"
             x-data="{
                isPublic: {{ $weatherStation->is_public ? 'true' : 'false' }},
                showCopied: false,
                publicUrl: '{{ route('netatmo.public', $weatherStation) }}',
                async togglePublic() {
                    try {
                        const response = await fetch('{{ route('netatmo.toggle-public', $weatherStation) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.isPublic = data.is_public;
                        }
                    } catch (error) {
                        console.error('Failed to toggle public access:', error);
                    }
                },
                copyUrl() {
                    navigator.clipboard.writeText(this.publicUrl);
                    this.showCopied = true;
                    setTimeout(() => this.showCopied = false, 2000);
                }
             }">
            <div class="px-6 py-4 border-b border-dark-border/50 bg-gradient-to-r from-dark-surface/60 to-purple-900/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-br from-purple-500/20 to-purple-600/20 p-2.5 rounded-lg border border-purple-500/30">
                            <i class="fas fa-share-alt text-purple-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Public Access</h3>
                            <p class="text-xs text-purple-300/70">Share your weather data publicly</p>
                        </div>
                    </div>

                    <!-- Toggle Switch -->
                    <button @click="togglePublic()"
                            class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors"
                            :class="isPublic ? 'bg-green-500' : 'bg-gray-600'">
                        <span class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform"
                              :class="isPublic ? 'translate-x-7' : 'translate-x-1'"></span>
                    </button>
                </div>
            </div>

            <div x-show="isPublic"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="px-6 py-4">
                <div class="flex items-center space-x-2">
                    <div class="flex-1 bg-dark-surface/60 rounded-lg px-4 py-3 border border-dark-border/30">
                        <p class="text-sm text-purple-200 break-all" x-text="publicUrl"></p>
                    </div>
                    <button @click="copyUrl()"
                            class="px-4 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium rounded-lg transition-all duration-200 shadow-lg shadow-purple-900/30 flex-shrink-0">
                        <i class="fas" :class="showCopied ? 'fa-check' : 'fa-copy'"></i>
                        <span class="ml-2" x-text="showCopied ? 'Copied!' : 'Copy'"></span>
                    </button>
                </div>
                <p class="text-xs text-purple-400/60 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Anyone with this link can view your weather station data
                </p>
            </div>
        </div>

        <!-- API Configuration Info -->
        <div class="mb-8 bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-xl border border-dark-border/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-dark-border/50 bg-gradient-to-r from-dark-surface/60 to-blue-900/10">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/20 p-2.5 rounded-lg border border-blue-500/30">
                        <i class="fas fa-key text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">API Configuration</h3>
                        <p class="text-xs text-blue-300/70">Authentication and connection details</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Client ID -->
                    <div class="bg-dark-surface/40 border border-blue-900/30 rounded-xl p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-id-badge text-blue-400 text-sm"></i>
                            <h4 class="text-sm font-semibold text-blue-300 uppercase tracking-wide">Client ID</h4>
                        </div>
                        <p class="text-white font-mono text-sm">{{ substr($weatherStation->client_id, 0, 8) }}••••••••</p>
                        <p class="text-xs text-blue-400/50 mt-1">Unique application identifier</p>
                    </div>

                    <!-- Device ID -->
                    <div class="bg-dark-surface/40 border border-blue-900/30 rounded-xl p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-broadcast-tower text-blue-400 text-sm"></i>
                            <h4 class="text-sm font-semibold text-blue-300 uppercase tracking-wide">Device ID</h4>
                        </div>
                        @if($weatherStation->device_id)
                            <p class="text-white font-mono text-sm">{{ $weatherStation->device_id }}</p>
                            <p class="text-xs text-blue-400/50 mt-1">Physical Netatmo device MAC address</p>
                        @else
                            <p class="text-orange-400 text-sm">Not selected</p>
                            <p class="text-xs text-orange-400/50 mt-1">Will auto-select on first authentication</p>
                        @endif
                    </div>

                    <!-- Token Status -->
                    <div class="bg-dark-surface/40 border border-blue-900/30 rounded-xl p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-shield-alt text-blue-400 text-sm"></i>
                            <h4 class="text-sm font-semibold text-blue-300 uppercase tracking-wide">Token Status</h4>
                        </div>
                        @if($weatherStation->token && $weatherStation->token->hasValidToken())
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                <p class="text-green-400 text-sm font-semibold">Active</p>
                            </div>
                            <p class="text-xs text-blue-400/50 mt-1">Expires {{ $weatherStation->token->expires_at->diffForHumans() }}</p>
                        @elseif($weatherStation->token)
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 bg-orange-400 rounded-full"></span>
                                <p class="text-orange-400 text-sm font-semibold">Expired</p>
                            </div>
                            <p class="text-xs text-orange-400/50 mt-1">Needs re-authentication</p>
                        @else
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                                <p class="text-red-400 text-sm font-semibold">Not Authenticated</p>
                            </div>
                            <p class="text-xs text-red-400/50 mt-1">Click "Refresh Data" to authenticate</p>
                        @endif
                    </div>

                    <!-- Last Update -->
                    <div class="bg-dark-surface/40 border border-blue-900/30 rounded-xl p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-clock text-blue-400 text-sm"></i>
                            <h4 class="text-sm font-semibold text-blue-300 uppercase tracking-wide">Last Data Fetch</h4>
                        </div>
                        @if($weatherStation->modules()->where('is_active', true)->exists())
                            @php
                                $latestModule = $weatherStation->modules()->where('is_active', true)->latest('updated_at')->first();
                            @endphp
                            <p class="text-white text-sm">{{ $latestModule->updated_at->diffForHumans() }}</p>
                            <p class="text-xs text-blue-400/50 mt-1">{{ $latestModule->updated_at->format('M d, Y H:i:s') }}</p>
                        @else
                            <p class="text-gray-400 text-sm">No data yet</p>
                            <p class="text-xs text-gray-400/50 mt-1">Authenticate to fetch data</p>
                        @endif
                    </div>
                </div>

                <div class="mt-4 bg-blue-900/20 border border-blue-700/30 rounded-xl p-3">
                    <p class="text-xs text-blue-300/70">
                        <i class="fas fa-info-circle mr-1"></i>
                        This station uses its own Netatmo API credentials and OAuth token. Data is fetched independently from other stations.
                    </p>
                </div>
            </div>
        </div>

        <!-- Weather Graphs (Hidden - now integrated into widgets) -->
        @if(false && $weatherStation->modules->where('is_active', true)->isNotEmpty())
        <div class="mb-8 hidden" x-data="weatherCharts()">
            <div class="flex items-center space-x-3 mb-6">
                <div class="bg-gradient-to-br from-purple-500/20 to-purple-600/20 p-2.5 rounded-lg border border-purple-500/30">
                    <i class="fas fa-chart-line text-purple-400 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Weather Trends</h2>
                    <p class="text-sm text-purple-300/70">Last 24 hours of measurements</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($weatherStation->modules->where('is_active', true) as $module)
                    @php
                        $dataTypes = $module->data_type ?? [];
                    @endphp

                    @if(in_array('Temperature', $dataTypes))
                    <!-- Temperature Chart -->
                    <div class="bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-xl border border-dark-border/50 overflow-hidden">
                        <div class="px-6 py-4 border-b border-dark-border/50 bg-gradient-to-r from-dark-surface/60 to-red-900/10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-temperature-high text-red-400 text-lg"></i>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Temperature</h3>
                                        <p class="text-xs text-red-300/70">{{ $module->module_name }}</p>
                                    </div>
                                </div>
                                <span class="text-2xl font-bold text-red-400">
                                    @if(isset($module->dashboard_data['Temperature']))
                                        {{ $module->dashboard_data['Temperature'] }}°C
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div x-show="!charts['temp-{{ $module->id }}']" class="flex items-center justify-center h-[200px]">
                                <div class="text-center">
                                    <i class="fas fa-spinner fa-spin text-red-400 text-2xl mb-2"></i>
                                    <p class="text-red-400/70 text-sm">Loading data...</p>
                                </div>
                            </div>
                            <canvas :id="'temp-{{ $module->id }}'" class="w-full" style="height: 200px;"></canvas>
                        </div>
                    </div>
                    @endif

                    @if(in_array('Humidity', $dataTypes))
                    <!-- Humidity Chart -->
                    <div class="bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-xl border border-dark-border/50 overflow-hidden">
                        <div class="px-6 py-4 border-b border-dark-border/50 bg-gradient-to-r from-dark-surface/60 to-blue-900/10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-tint text-blue-400 text-lg"></i>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Humidity</h3>
                                        <p class="text-xs text-blue-300/70">{{ $module->module_name }}</p>
                                    </div>
                                </div>
                                <span class="text-2xl font-bold text-blue-400">
                                    @if(isset($module->dashboard_data['Humidity']))
                                        {{ $module->dashboard_data['Humidity'] }}%
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <canvas :id="'humidity-{{ $module->id }}'" class="w-full" style="height: 200px;"></canvas>
                        </div>
                    </div>
                    @endif

                    @if(in_array('CO2', $dataTypes))
                    <!-- CO2 Chart -->
                    <div class="bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-xl border border-dark-border/50 overflow-hidden">
                        <div class="px-6 py-4 border-b border-dark-border/50 bg-gradient-to-r from-dark-surface/60 to-green-900/10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-wind text-green-400 text-lg"></i>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">CO₂ Levels</h3>
                                        <p class="text-xs text-green-300/70">{{ $module->module_name }}</p>
                                    </div>
                                </div>
                                <span class="text-2xl font-bold text-green-400">
                                    @if(isset($module->dashboard_data['CO2']))
                                        {{ $module->dashboard_data['CO2'] }} ppm
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <canvas :id="'co2-{{ $module->id }}'" class="w-full" style="height: 200px;"></canvas>
                        </div>
                    </div>
                    @endif

                    @if(in_array('Rain', $dataTypes))
                    <!-- Rain Chart (Bar Chart) -->
                    <div class="bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-xl border border-dark-border/50 overflow-hidden">
                        <div class="px-6 py-4 border-b border-dark-border/50 bg-gradient-to-r from-dark-surface/60 to-cyan-900/10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-cloud-rain text-cyan-400 text-lg"></i>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Rainfall</h3>
                                        <p class="text-xs text-cyan-300/70">{{ $module->module_name }}</p>
                                    </div>
                                </div>
                                <span class="text-2xl font-bold text-cyan-400">
                                    @if(isset($module->dashboard_data['Rain']))
                                        {{ $module->dashboard_data['Rain'] }} mm
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <canvas :id="'rain-{{ $module->id }}'" class="w-full" style="height: 200px;"></canvas>
                        </div>
                    </div>
                    @endif

                    @if(in_array('WindStrength', $dataTypes))
                    <!-- Wind Speed Chart -->
                    <div class="bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-xl border border-dark-border/50 overflow-hidden">
                        <div class="px-6 py-4 border-b border-dark-border/50 bg-gradient-to-r from-dark-surface/60 to-purple-900/10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-wind text-purple-400 text-lg"></i>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Wind Speed</h3>
                                        <p class="text-xs text-purple-300/70">{{ $module->module_name }}</p>
                                    </div>
                                </div>
                                <span class="text-2xl font-bold text-purple-400">
                                    @if(isset($module->dashboard_data['WindStrength']))
                                        {{ $module->dashboard_data['WindStrength'] }} km/h
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <canvas :id="'wind-{{ $module->id }}'" class="w-full" style="height: 200px;"></canvas>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

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
                 x-data="{ expanded: false }">
                <div class="px-6 py-4 border-b border-dark-border/50 bg-gradient-to-r from-dark-surface/60 to-orange-900/10">
                    <div class="flex items-center justify-between cursor-pointer" @click="expanded = !expanded">
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
                        <div class="transition-transform duration-200"
                             :class="expanded ? 'rotate-180' : ''">
                            <i class="fas fa-chevron-down text-purple-400"></i>
                        </div>
                    </div>
                </div>

                <div x-show="expanded"
                     x-collapse
                     class="px-6 py-4">
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
                                        <form method="POST" action="{{ route('netatmo.modules.activate', [$weatherStation, $module]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center space-x-2 px-4 py-2 bg-green-900/20 hover:bg-green-900/40 border border-green-500/30 hover:border-green-500/50 text-green-400 hover:text-green-300 text-sm font-medium rounded-lg transition-all duration-200">
                                                <i class="fas fa-check-circle"></i>
                                                <span>Reactivate</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('netatmo.modules.destroy', [$weatherStation, $module]) }}"
                                              onsubmit="return confirm('Are you sure you want to permanently delete this module?');">
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
                            These modules are no longer detected by the Netatmo API. They may have been removed, lost connection, or have dead batteries.
                            You can safely delete them if they're no longer needed.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Mini chart component for module widgets
        window.miniChart = function(moduleId, dataType, color, unit) {
            return {
                loading: true,
                chart: null,

                init() {
                    // Wait for Chart.js
                    if (typeof Chart === 'undefined') {
                        setTimeout(() => this.init(), 100);
                        return;
                    }

                    this.loadData();
                },

                async loadData() {
                    try {
                        const response = await fetch(`/api/netatmo/stations/{{ $weatherStation->uuid }}/modules/${moduleId}/measurements?period=1day&scale=1hour`);
                        const data = await response.json();

                        if (data.error) {
                            console.error('API error for mini chart:', data);
                            this.loading = false;
                            return;
                        }

                        if (data.measurements && data.measurements.data[dataType]) {
                            this.createChart(data.measurements.timestamps, data.measurements.data[dataType], color, unit);
                        }
                    } catch (error) {
                        console.error('Failed to load mini chart:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                createChart(timestamps, values, color, unit) {
                    const canvas = this.$refs.canvas;
                    if (!canvas) return;

                    // Format timestamps to show only hours
                    const labels = timestamps.map(t => {
                        const date = new Date(t);
                        return date.getHours() + ':00';
                    });

                    const ctx = canvas.getContext('2d');
                    this.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                borderColor: color,
                                backgroundColor: color + '20',
                                borderWidth: 1.5,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 3,
                                pointHoverBackgroundColor: color,
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 1,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 8,
                                    titleFont: { size: 10 },
                                    bodyFont: { size: 11 },
                                    displayColors: false,
                                    callbacks: {
                                        label: (context) => `${context.parsed.y}${unit}`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    display: false,
                                },
                                y: {
                                    display: false,
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                }
            }
        };

        // Define weatherCharts globally before Alpine.js initializes
        window.weatherCharts = function() {
            return {
                charts: {},
                init() {
                    // Wait for Chart.js to be ready
                    if (typeof Chart === 'undefined') {
                        console.log('Waiting for Chart.js to load...');
                        setTimeout(() => this.init(), 100);
                        return;
                    }

                    console.log('Chart.js loaded, initializing weather charts...');

                    // Initialize charts for each module
                    @foreach($weatherStation->modules->where('is_active', true) as $module)
                        this.initModuleCharts({{ $module->id }}, '{{ $module->module_id }}', {!! json_encode($module->data_type ?? []) !!});
                    @endforeach
                },

                async initModuleCharts(moduleDbId, moduleId, dataTypes) {
                    try {
                        const url = `/api/netatmo/stations/{{ $weatherStation->uuid }}/modules/${moduleId}/measurements` + '?period=1day' + '&scale=30min';
                        console.log('Fetching measurements for module:', moduleId, 'URL:', url);

                        const response = await fetch(url);
                        console.log('Response status:', response.status);

                        const data = await response.json();
                        console.log('Response data:', data);

                        if (data.measurements && data.measurements.timestamps && data.measurements.timestamps.length > 0) {
                            const timestamps = data.measurements.timestamps.map(t => new Date(t).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }));
                            console.log('Creating charts for module', moduleDbId, 'with', timestamps.length, 'data points');

                            // Temperature Chart
                            if (dataTypes.includes('Temperature') && data.measurements.data.Temperature) {
                                this.createLineChart(`temp-${moduleDbId}`, timestamps, data.measurements.data.Temperature, 'Temperature', '#ef4444', '°C');
                            }

                            // Humidity Chart
                            if (dataTypes.includes('Humidity') && data.measurements.data.Humidity) {
                                this.createLineChart(`humidity-${moduleDbId}`, timestamps, data.measurements.data.Humidity, 'Humidity', '#3b82f6', '%');
                            }

                            // CO2 Chart
                            if (dataTypes.includes('CO2') && data.measurements.data.CO2) {
                                this.createLineChart(`co2-${moduleDbId}`, timestamps, data.measurements.data.CO2, 'CO₂', '#10b981', ' ppm');
                            }

                            // Rain Chart (Bar Chart)
                            if (dataTypes.includes('Rain') && data.measurements.data.Rain) {
                                this.createBarChart(`rain-${moduleDbId}`, timestamps, data.measurements.data.Rain, 'Rainfall', '#06b6d4', ' mm');
                            }

                            // Wind Speed Chart
                            if (dataTypes.includes('WindStrength') && data.measurements.data.WindStrength) {
                                this.createLineChart(`wind-${moduleDbId}`, timestamps, data.measurements.data.WindStrength, 'Wind Speed', '#a855f7', ' km/h');
                            }
                        } else {
                            console.warn('No measurement data available for module', moduleId, data);
                            // Mark charts as attempted for this module to hide loading spinners
                            dataTypes.forEach(type => {
                                const chartId = this.getChartId(type, moduleDbId);
                                if (chartId) {
                                    this.charts[chartId] = 'no-data'; // Mark as attempted but no data
                                    this.showNoDataMessage(chartId, type);
                                }
                            });
                        }
                    } catch (error) {
                        console.error(`Failed to fetch measurements for module ${moduleId}:`, error);
                        // Mark all charts as failed to hide loading spinners
                        dataTypes.forEach(type => {
                            const chartId = this.getChartId(type, moduleDbId);
                            if (chartId) {
                                this.charts[chartId] = 'error';
                                this.showErrorMessage(chartId, error.message);
                            }
                        });
                    }
                },

                getChartId(dataType, moduleDbId) {
                    const typeMap = {
                        'Temperature': `temp-${moduleDbId}`,
                        'Humidity': `humidity-${moduleDbId}`,
                        'CO2': `co2-${moduleDbId}`,
                        'Rain': `rain-${moduleDbId}`,
                        'WindStrength': `wind-${moduleDbId}`,
                    };
                    return typeMap[dataType] || null;
                },

                showNoDataMessage(canvasId, dataType) {
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;

                    const parent = canvas.parentElement;
                    const message = document.createElement('div');
                    message.className = 'flex items-center justify-center h-[200px]';
                    message.innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-database text-gray-500 text-2xl mb-2"></i>
                            <p class="text-gray-400 text-sm">No historical data available yet</p>
                            <p class="text-gray-500 text-xs mt-1">Data will be collected over time</p>
                        </div>
                    `;
                    canvas.style.display = 'none';
                    parent.appendChild(message);
                },

                showErrorMessage(canvasId, errorMsg) {
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;

                    const parent = canvas.parentElement;
                    const message = document.createElement('div');
                    message.className = 'flex items-center justify-center h-[200px]';
                    message.innerHTML = `
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle text-orange-500 text-2xl mb-2"></i>
                            <p class="text-orange-400 text-sm">Failed to load chart data</p>
                            <p class="text-orange-500 text-xs mt-1">${errorMsg || 'Unknown error'}</p>
                        </div>
                    `;
                    canvas.style.display = 'none';
                    parent.appendChild(message);
                },

                createLineChart(canvasId, labels, data, label, color, unit) {
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) {
                        console.error('Canvas not found:', canvasId);
                        return;
                    }

                    console.log('Creating line chart:', canvasId, 'with', data.length, 'data points');

                    const ctx = canvas.getContext('2d');
                    this.charts[canvasId] = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: label,
                                data: data,
                                borderColor: color,
                                backgroundColor: color + '20',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                                pointHoverBackgroundColor: color,
                                pointHoverBorderColor: '#fff',
                                pointHoverBorderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: color,
                                    borderWidth: 1,
                                    callbacks: {
                                        label: (context) => `${context.parsed.y}${unit}`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        color: 'rgba(139, 92, 246, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: '#94a3b8',
                                        maxRotation: 0,
                                        maxTicksLimit: 8
                                    }
                                },
                                y: {
                                    grid: {
                                        color: 'rgba(139, 92, 246, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: '#94a3b8',
                                        callback: (value) => value + unit
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                },

                createBarChart(canvasId, labels, data, label, color, unit) {
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;

                    const ctx = canvas.getContext('2d');
                    this.charts[canvasId] = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: label,
                                data: data,
                                backgroundColor: color + '80',
                                borderColor: color,
                                borderWidth: 1,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    borderColor: color,
                                    borderWidth: 1,
                                    callbacks: {
                                        label: (context) => `${context.parsed.y}${unit}`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        color: 'rgba(139, 92, 246, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: '#94a3b8',
                                        maxRotation: 0,
                                        maxTicksLimit: 8
                                    }
                                },
                                y: {
                                    grid: {
                                        color: 'rgba(139, 92, 246, 0.1)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        color: '#94a3b8',
                                        callback: (value) => value + unit
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
@endsection

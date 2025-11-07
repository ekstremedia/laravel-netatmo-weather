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
@endsection

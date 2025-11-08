{{-- Additional Indoor Module (NAModule4) --}}
<div class="bg-gradient-to-br from-dark-elevated/90 to-dark-elevated/70 backdrop-blur-xl rounded-3xl shadow-2xl shadow-violet-900/20 border border-violet-500/20 overflow-hidden">

    {{-- Header with Status --}}
    <div class="bg-gradient-to-r from-violet-500/20 via-fuchsia-500/20 to-purple-500/20 px-6 py-4 border-b border-violet-500/30">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-violet-500 via-fuchsia-500 to-purple-500 p-3 rounded-xl shadow-lg">
                    <img src="{{ asset('netatmo-weather/images/icons/indoor.svg') }}"
                         alt="Indoor Module"
                         class="w-8 h-8">
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $module->module_name }}</h3>
                    @if($module->dashboard_data)
                        <p class="text-sm text-violet-200/70 flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Updated @datetime($module->dashboard_data['time_utc'])
                        </p>
                    @else
                        <p class="text-sm text-violet-200/70 flex items-center mt-1">
                            Last seen: @datetime($module->last_seen)
                        </p>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @if($module->reachable)
                    <div class="bg-green-500/20 px-4 py-2 rounded-full border border-green-400/30 backdrop-blur-sm">
                        <span class="text-green-300 text-sm font-semibold flex items-center">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                            Online
                        </span>
                    </div>
                @else
                    <div class="bg-red-500/20 px-4 py-2 rounded-full border border-red-400/30 backdrop-blur-sm">
                        <span class="text-red-300 text-sm font-semibold flex items-center">
                            <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                            Offline
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    @if($module->dashboard_data)
        <div class="p-4 space-y-4">

            {{-- Primary Metrics --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                {{-- Temperature Card --}}
                <div class="bg-gradient-to-br from-rose-500/10 to-pink-500/10 rounded-2xl p-4 border border-rose-500/20"
                     x-data="miniChart('{{ $module->module_id }}', 'Temperature', '#f43f5e', '°C')">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex-1 min-w-0">
                            <div class="text-rose-300/80 text-xs font-medium uppercase tracking-wide mb-1.5">Temperature</div>
                            <div class="text-3xl md:text-4xl font-bold text-white leading-none">{{ $module->dashboard_data['Temperature'] }}<span class="text-lg md:text-xl text-rose-200/60">°C</span></div>
                        </div>
                        <div class="bg-rose-500/20 p-2 rounded-xl flex-shrink-0">
                            <svg class="w-6 h-6 text-rose-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15 13V5c0-1.66-1.34-3-3-3S9 3.34 9 5v8c-1.21.91-2 2.37-2 4 0 2.76 2.24 5 5 5s5-2.24 5-5c0-1.63-.79-3.09-2-4zm-4-8c0-.55.45-1 1-1s1 .45 1 1v8.5l.5.25c.86.43 1.5 1.28 1.5 2.25 0 1.38-1.12 2.5-2.5 2.5S10 17.38 10 16c0-.97.64-1.82 1.5-2.25l.5-.25V5z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Mini Chart --}}
                    <div class="mt-2 mb-2 h-12 relative">
                        <canvas x-ref="canvas" class="w-full h-full"></canvas>
                        <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-dark-surface/40 rounded">
                            <div class="w-3 h-3 border-2 border-rose-400 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs gap-2">
                        <span class="text-blue-300 flex items-center whitespace-nowrap">
                            <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                            {{ $module->dashboard_data['min_temp'] }}°C
                        </span>
                        <span class="text-rose-200/50 text-[10px]">24h</span>
                        <span class="text-red-300 flex items-center whitespace-nowrap">
                            <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            {{ $module->dashboard_data['max_temp'] }}°C
                        </span>
                    </div>
                </div>

                {{-- Humidity Card --}}
                <div class="bg-gradient-to-br from-violet-500/10 to-purple-500/10 rounded-2xl p-4 border border-violet-500/20"
                     x-data="miniChart('{{ $module->module_id }}', 'Humidity', '#8b5cf6', '%')">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex-1 min-w-0">
                            <div class="text-violet-300/80 text-xs font-medium uppercase tracking-wide mb-1.5">Humidity</div>
                            <div class="text-3xl md:text-4xl font-bold text-white leading-none">{{ $module->dashboard_data['Humidity'] }}<span class="text-lg md:text-xl text-violet-200/60">%</span></div>
                        </div>
                        <div class="bg-violet-500/20 p-2 rounded-xl flex-shrink-0">
                            <svg class="w-6 h-6 text-violet-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Mini Chart --}}
                    <div class="mt-2 mb-2 h-12 relative">
                        <canvas x-ref="canvas" class="w-full h-full"></canvas>
                        <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-dark-surface/40 rounded">
                            <div class="w-3 h-3 border-2 border-violet-400 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <div class="w-full bg-dark-surface/40 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-violet-400 to-purple-400 h-2 rounded-full transition-all duration-500" style="width: {{ $module->dashboard_data['Humidity'] }}%"></div>
                        </div>
                        <p class="text-[10px] text-violet-200/60 mt-1.5">
                            @if($module->dashboard_data['Humidity'] < 30)
                                Dry
                            @elseif($module->dashboard_data['Humidity'] < 60)
                                Comfortable
                            @else
                                Humid
                            @endif
                        </p>
                    </div>
                </div>

                {{-- CO2 Card --}}
                <div class="bg-gradient-to-br from-fuchsia-500/10 to-pink-500/10 rounded-2xl p-4 border border-fuchsia-500/20"
                     x-data="miniChart('{{ $module->module_id }}', 'CO2', '#d946ef', ' ppm')">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex-1 min-w-0">
                            <div class="text-fuchsia-300/80 text-xs font-medium uppercase tracking-wide mb-1.5">CO₂ Level</div>
                            <div class="text-3xl md:text-4xl font-bold text-white leading-none">{{ $module->dashboard_data['CO2'] }}<span class="text-base md:text-lg text-fuchsia-200/60">ppm</span></div>
                        </div>
                        <div class="bg-fuchsia-500/20 p-2 rounded-xl flex-shrink-0">
                            <svg class="w-6 h-6 text-fuchsia-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Mini Chart --}}
                    <div class="mt-2 mb-2 h-12 relative">
                        <canvas x-ref="canvas" class="w-full h-full"></canvas>
                        <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-dark-surface/40 rounded">
                            <div class="w-3 h-3 border-2 border-fuchsia-400 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <p class="text-[10px] text-fuchsia-200/60 uppercase mb-0.5">Air Quality</p>
                        <p class="text-xs font-semibold
                            @if($module->dashboard_data['CO2'] < 800) text-green-300
                            @elseif($module->dashboard_data['CO2'] < 1500) text-yellow-300
                            @else text-red-300
                            @endif">
                            @if($module->dashboard_data['CO2'] < 800)
                                Excellent
                            @elseif($module->dashboard_data['CO2'] < 1500)
                                Good
                            @else
                                Poor - Ventilate
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Status Section --}}
            <div class="grid grid-cols-3 gap-3">
                {{-- Battery --}}
                <div class="bg-dark-surface/40 rounded-xl p-3 border border-dark-border/30 hover:border-violet-500/30 transition-colors">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-purple-300/80">Battery</span>
                        <svg class="w-4 h-4
                            @if($module->battery_percent > 50) text-green-400
                            @elseif($module->battery_percent > 20) text-amber-400
                            @else text-red-400
                            @endif"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div class="text-xl font-bold text-white leading-none">{{ $module->battery_percent }}%</div>
                    <div class="w-full bg-dark-surface/60 rounded-full h-1.5 mt-1.5 overflow-hidden">
                        <div class="
                            @if($module->battery_percent > 50) bg-green-400
                            @elseif($module->battery_percent > 20) bg-amber-400
                            @else bg-red-400
                            @endif h-1.5 rounded-full transition-all duration-500"
                            style="width: {{ $module->battery_percent }}%"></div>
                    </div>
                </div>

                {{-- RF Signal --}}
                <div class="bg-dark-surface/40 rounded-xl p-3 border border-dark-border/30 hover:border-purple-500/30 transition-colors">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-purple-300/80">RF Signal</span>
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                        </svg>
                    </div>
                    <div class="text-xl font-bold text-white leading-none">{{ $module->rf_status }}</div>
                    <div class="text-[10px] text-purple-400/60 mt-1">Signal strength</div>
                </div>

                {{-- Firmware --}}
                <div class="bg-dark-surface/40 rounded-xl p-3 border border-dark-border/30 hover:border-fuchsia-500/30 transition-colors">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-purple-300/80">Firmware</span>
                        <svg class="w-4 h-4 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-xl font-bold text-white leading-none">{{ $module->firmware }}</div>
                    <div class="text-[10px] text-fuchsia-400/60 mt-1">
                        @if($module->reachable)
                            <span class="text-green-400">● Connected</span>
                        @else
                            <span class="text-red-400">● Disconnected</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="p-6">
            @include('netatmoweather::netatmo.widgets.MissingData')
        </div>
    @endif
</div>

{{-- Additional Indoor Module (NAModule4) --}}
<div class="bg-gradient-to-br from-dark-elevated/90 to-dark-elevated/70 backdrop-blur-xl rounded-3xl shadow-2xl shadow-violet-900/20 border border-violet-500/20 overflow-hidden">

    {{-- Header with Status --}}
    <div class="bg-gradient-to-r from-violet-500/20 via-fuchsia-500/20 to-purple-500/20 px-6 py-4 border-b border-violet-500/30">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-violet-500 via-fuchsia-500 to-purple-500 p-3 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
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
        <div class="p-6 space-y-6">

            {{-- Primary Metrics --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Temperature Card --}}
                <div class="bg-gradient-to-br from-rose-500/10 to-pink-500/10 rounded-2xl p-6 border border-rose-500/20">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex-1 min-w-0">
                            <div class="text-rose-300/80 text-sm font-medium uppercase tracking-wide mb-1">Temperature</div>
                            <div class="text-4xl md:text-5xl font-bold text-white">{{ $module->dashboard_data['Temperature'] }}<span class="text-xl md:text-2xl text-rose-200/60">°C</span></div>
                        </div>
                        <div class="bg-rose-500/20 p-2.5 rounded-xl flex-shrink-0">
                            <svg class="w-7 h-7 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-blue-300 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                            {{ $module->dashboard_data['min_temp'] }}°C
                        </span>
                        <span class="text-rose-200/50 text-xs">@time($module->dashboard_data['date_min_temp'])</span>
                        <span class="text-red-300 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            {{ $module->dashboard_data['max_temp'] }}°C
                        </span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-rose-500/20">
                        <span class="text-xs text-rose-200/60 uppercase">Trend: </span>
                        <span class="text-sm font-semibold text-rose-200">{{ $module->dashboard_data['temp_trend'] }}</span>
                    </div>
                </div>

                {{-- Humidity Card --}}
                <div class="bg-gradient-to-br from-violet-500/10 to-purple-500/10 rounded-2xl p-6 border border-violet-500/20">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex-1 min-w-0">
                            <div class="text-violet-300/80 text-sm font-medium uppercase tracking-wide mb-1">Humidity</div>
                            <div class="text-4xl md:text-5xl font-bold text-white">{{ $module->dashboard_data['Humidity'] }}<span class="text-xl md:text-2xl text-violet-200/60">%</span></div>
                        </div>
                        <div class="bg-violet-500/20 p-2.5 rounded-xl flex-shrink-0">
                            <svg class="w-7 h-7 text-violet-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <div class="w-full bg-dark-surface/40 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-violet-400 to-purple-400 h-2.5 rounded-full transition-all duration-500" style="width: {{ $module->dashboard_data['Humidity'] }}%"></div>
                        </div>
                        <p class="text-xs text-violet-200/60 mt-2">
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
                <div class="bg-gradient-to-br from-fuchsia-500/10 to-pink-500/10 rounded-2xl p-6 border border-fuchsia-500/20">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex-1 min-w-0">
                            <div class="text-fuchsia-300/80 text-sm font-medium uppercase tracking-wide mb-1">CO₂ Level</div>
                            <div class="text-4xl md:text-5xl font-bold text-white">{{ $module->dashboard_data['CO2'] }}<span class="text-lg md:text-xl text-fuchsia-200/60">ppm</span></div>
                        </div>
                        <div class="bg-fuchsia-500/20 p-2.5 rounded-xl flex-shrink-0">
                            <svg class="w-7 h-7 text-fuchsia-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <p class="text-xs text-fuchsia-200/60 uppercase mb-1">Air Quality</p>
                        <p class="text-sm font-semibold
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
            <div class="grid grid-cols-3 gap-4">
                {{-- Battery --}}
                <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30 hover:border-violet-500/30 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-purple-300/80">Battery</span>
                        <svg class="w-5 h-5
                            @if($module->battery_percent > 50) text-green-400
                            @elseif($module->battery_percent > 20) text-amber-400
                            @else text-red-400
                            @endif"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ $module->battery_percent }}%</div>
                    <div class="w-full bg-dark-surface/60 rounded-full h-1.5 mt-2 overflow-hidden">
                        <div class="
                            @if($module->battery_percent > 50) bg-green-400
                            @elseif($module->battery_percent > 20) bg-amber-400
                            @else bg-red-400
                            @endif h-1.5 rounded-full transition-all duration-500"
                            style="width: {{ $module->battery_percent }}%"></div>
                    </div>
                </div>

                {{-- RF Signal --}}
                <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30 hover:border-purple-500/30 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-purple-300/80">RF Signal</span>
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ $module->rf_status }}</div>
                    <div class="text-xs text-purple-400/60 mt-1">Signal strength</div>
                </div>

                {{-- Firmware --}}
                <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30 hover:border-fuchsia-500/30 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-purple-300/80">Firmware</span>
                        <svg class="w-5 h-5 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ $module->firmware }}</div>
                    <div class="text-xs text-fuchsia-400/60 mt-1">
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

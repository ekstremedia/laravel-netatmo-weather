{{-- Wind Gauge Module (NAModule2) --}}
<div class="bg-gradient-to-br from-dark-elevated/90 to-dark-elevated/70 backdrop-blur-xl rounded-3xl shadow-2xl shadow-emerald-900/20 border border-emerald-500/20 overflow-hidden">

    {{-- Header with Status --}}
    <div class="bg-gradient-to-r from-emerald-500/20 via-teal-500/20 to-green-500/20 px-6 py-4 border-b border-emerald-500/30">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-emerald-500 via-teal-500 to-green-500 p-3 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $module->module_name }}</h3>
                    @if($module->dashboard_data)
                        <p class="text-sm text-emerald-200/70 flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Updated @datetime($module->dashboard_data['time_utc'])
                        </p>
                    @else
                        <p class="text-sm text-emerald-200/70 flex items-center mt-1">
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

            {{-- Primary Wind Metrics --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Current Wind Speed --}}
                <div class="bg-gradient-to-br from-emerald-500/10 to-teal-500/10 rounded-2xl p-6 border border-emerald-500/20">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-emerald-300/80 text-sm font-medium uppercase tracking-wide mb-1">Wind Speed</div>
                            <div class="text-5xl font-bold text-white">{{ $module->dashboard_data['WindStrength'] ?? 'N/A' }}<span class="text-xl text-emerald-200/60">km/h</span></div>
                        </div>
                        <div class="bg-emerald-500/20 p-3 rounded-xl">
                            <svg class="w-8 h-8 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-center">
                            <div class="text-teal-400 text-sm mb-1">Gust</div>
                            <div class="text-xl font-bold text-white">{{ $module->dashboard_data['GustStrength'] ?? 'N/A' }}<span class="text-sm text-teal-200/60">km/h</span></div>
                        </div>
                        <div class="text-center">
                            <div class="text-teal-400 text-sm mb-1">Max Wind</div>
                            <div class="text-xl font-bold text-white">{{ $module->dashboard_data['max_wind_str'] ?? 'N/A' }}<span class="text-sm text-teal-200/60">km/h</span></div>
                        </div>
                    </div>
                </div>

                {{-- Wind Direction --}}
                <div class="bg-gradient-to-br from-green-500/10 to-cyan-500/10 rounded-2xl p-6 border border-green-500/20">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-green-300/80 text-sm font-medium uppercase tracking-wide mb-1">Wind Direction</div>
                            <div class="text-5xl font-bold text-white">{{ $module->dashboard_data['WindAngle'] ?? 'N/A' }}<span class="text-2xl text-green-200/60">°</span></div>
                        </div>
                        <div class="bg-green-500/20 p-3 rounded-xl">
                            <svg class="w-8 h-8 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="transform: rotate({{ $module->dashboard_data['WindAngle'] ?? 0 }}deg)">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-center pt-3 border-t border-green-500/20">
                        <span class="text-sm font-semibold text-green-200">
                            @php
                                $angle = $module->dashboard_data['WindAngle'] ?? 0;
                                $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
                                $index = round($angle / 22.5) % 16;
                            @endphp
                            {{ $directions[$index] }}
                        </span>
                        <span class="text-xs text-green-200/60 ml-2">({{ ucfirst($directions[$index]) }})</span>
                    </div>
                </div>
            </div>

            {{-- Historical Wind Data --}}
            @if(isset($module->dashboard_data['date_max_wind_str']))
                <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-purple-300/80 mb-1">Peak Wind Today</div>
                            <div class="text-2xl font-bold text-white">{{ $module->dashboard_data['max_wind_str'] ?? 'N/A' }} km/h</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-purple-300/80 mb-1">Recorded At</div>
                            <div class="text-sm font-semibold text-purple-200">@time($module->dashboard_data['date_max_wind_str'])</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Status Section --}}
            <div class="grid grid-cols-3 gap-4">
                {{-- Battery --}}
                <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30 hover:border-emerald-500/30 transition-colors">
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
                <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30 hover:border-teal-500/30 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-purple-300/80">Firmware</span>
                        <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ $module->firmware }}</div>
                    <div class="text-xs text-teal-400/60 mt-1">
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

{{-- Wind Gauge Module (NAModule2) --}}
<div class="bg-gradient-to-br from-dark-elevated/90 to-dark-elevated/70 backdrop-blur-xl rounded-3xl shadow-2xl shadow-emerald-900/20 border border-emerald-500/20 overflow-hidden">

    {{-- Header with Status --}}
    <div class="bg-gradient-to-r from-emerald-500/20 via-teal-500/20 to-green-500/20 px-6 py-4 border-b border-emerald-500/30">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-emerald-500 via-teal-500 to-green-500 p-3 rounded-xl shadow-lg">
                    <img src="{{ asset('netatmo-weather/images/icons/wind.svg') }}"
                         alt="Wind Gauge"
                         class="w-8 h-8">
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
        <div class="p-4 space-y-4">

            {{-- Primary Wind Metrics --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                {{-- Current Wind Speed --}}
                @php
                    $windSpeed = isset($module->dashboard_data['WindStrength']) ? round($module->dashboard_data['WindStrength'] / 3.6, 1) : null;
                    $gustSpeed = isset($module->dashboard_data['GustStrength']) ? round($module->dashboard_data['GustStrength'] / 3.6, 1) : null;
                    $maxWindSpeed = isset($module->dashboard_data['max_wind_str']) ? round($module->dashboard_data['max_wind_str'] / 3.6, 1) : null;
                @endphp
                <div class="bg-gradient-to-br from-emerald-500/10 to-teal-500/10 rounded-2xl p-4 border border-emerald-500/20"
                     x-data="miniChart('{{ $module->module_id }}', 'WindStrength', '#10b981', ' m/s')">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex-1 min-w-0">
                            <div class="text-emerald-300/80 text-xs font-medium uppercase tracking-wide mb-1.5">Wind Speed</div>
                            <div class="text-3xl md:text-4xl font-bold text-white leading-none">{{ $windSpeed ?? 'N/A' }}<span class="text-base md:text-lg text-emerald-200/60">m/s</span></div>
                        </div>
                        <div class="bg-emerald-500/20 p-2 rounded-xl flex-shrink-0">
                            <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Mini Chart --}}
                    <div class="mt-2 mb-2 h-16 relative">
                        <canvas x-ref="canvas" class="w-full h-full"></canvas>
                        <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-dark-surface/40 rounded">
                            <div class="w-4 h-4 border-2 border-emerald-400 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <div class="text-center">
                            <div class="text-teal-400 text-[10px] mb-0.5">Gust</div>
                            <div class="text-base font-bold text-white leading-none">{{ $gustSpeed ?? 'N/A' }}<span class="text-xs text-teal-200/60">m/s</span></div>
                        </div>
                        <div class="text-center">
                            <div class="text-teal-400 text-[10px] mb-0.5">Max Wind</div>
                            <div class="text-base font-bold text-white leading-none">{{ $maxWindSpeed ?? 'N/A' }}<span class="text-xs text-teal-200/60">m/s</span></div>
                        </div>
                    </div>
                </div>

                {{-- Wind Direction --}}
                <div class="bg-gradient-to-br from-green-500/10 to-cyan-500/10 rounded-2xl p-4 border border-green-500/20">
                    <div class="text-green-300/80 text-xs font-medium uppercase tracking-wide mb-3 text-center">Wind Direction</div>

                    {{-- Large Central Compass Arrow --}}
                    <div class="flex flex-col items-center justify-center mb-3">
                        <div class="relative">
                            <svg class="w-24 h-24 text-green-400/70" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="transform: rotate({{ $module->dashboard_data['WindAngle'] ?? 0 }}deg)">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-white mt-2">{{ $module->dashboard_data['WindAngle'] ?? 'N/A' }}<span class="text-lg text-green-200/60">°</span></div>
                    </div>

                    <div class="text-center pt-2 border-t border-green-500/20">
                        <span class="text-lg font-semibold text-green-200">
                            @php
                                $angle = $module->dashboard_data['WindAngle'] ?? 0;
                                $directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
                                $index = round($angle / 22.5) % 16;
                            @endphp
                            {{ $directions[$index] }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Historical Wind Data --}}
            @if(isset($module->dashboard_data['date_max_wind_str']))
                <div class="bg-dark-surface/40 rounded-xl p-3 border border-dark-border/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-purple-300/80 mb-0.5">Peak Wind Today</div>
                            <div class="text-xl font-bold text-white leading-none">{{ round($module->dashboard_data['max_wind_str'] / 3.6, 1) ?? 'N/A' }} m/s</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-purple-300/80 mb-0.5">Recorded At</div>
                            <div class="text-xs font-semibold text-purple-200">@time($module->dashboard_data['date_max_wind_str'])</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Status Section --}}
            <div class="grid grid-cols-3 gap-3">
                {{-- Battery --}}
                <div class="bg-dark-surface/40 rounded-xl p-3 border border-dark-border/30 hover:border-emerald-500/30 transition-colors">
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
                <div class="bg-dark-surface/40 rounded-xl p-3 border border-dark-border/30 hover:border-teal-500/30 transition-colors">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-purple-300/80">Firmware</span>
                        <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-xl font-bold text-white leading-none">{{ $module->firmware }}</div>
                    <div class="text-[10px] text-teal-400/60 mt-1">
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

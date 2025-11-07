{{-- Rain Gauge Module (NAModule3) --}}
<div class="bg-gradient-to-br from-dark-elevated/90 to-dark-elevated/70 backdrop-blur-xl rounded-3xl shadow-2xl shadow-sky-900/20 border border-sky-500/20 overflow-hidden">

    {{-- Header with Status --}}
    <div class="bg-gradient-to-r from-sky-500/20 via-blue-500/20 to-indigo-500/20 px-6 py-4 border-b border-sky-500/30">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-sky-500 via-blue-500 to-indigo-500 p-3 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 008 10.586V5L7 4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $module->module_name }}</h3>
                    @if($module->dashboard_data)
                        <p class="text-sm text-sky-200/70 flex items-center mt-1">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Updated @datetime($module->dashboard_data['time_utc'])
                        </p>
                    @else
                        <p class="text-sm text-sky-200/70 flex items-center mt-1">
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

            {{-- Primary Rain Metrics --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Current Rain --}}
                <div class="bg-gradient-to-br from-sky-500/10 to-blue-500/10 rounded-2xl p-6 border border-sky-500/20">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-sky-300/80 text-sm font-medium uppercase tracking-wide mb-1">Current</div>
                            <div class="text-5xl font-bold text-white">{{ $module->dashboard_data['Rain'] ?? '0' }}<span class="text-2xl text-sky-200/60">mm</span></div>
                        </div>
                        <div class="bg-sky-500/20 p-3 rounded-xl">
                            <svg class="w-8 h-8 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 008 10.586V5L7 4z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-xs text-sky-200/60 uppercase">Rainfall amount</div>
                </div>

                {{-- Last Hour --}}
                <div class="bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-2xl p-6 border border-blue-500/20">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-blue-300/80 text-sm font-medium uppercase tracking-wide mb-1">Last Hour</div>
                            <div class="text-5xl font-bold text-white">{{ $module->dashboard_data['sum_rain_1'] ?? '0' }}<span class="text-2xl text-blue-200/60">mm</span></div>
                        </div>
                        <div class="bg-blue-500/20 p-3 rounded-xl">
                            <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-xs text-blue-200/60 uppercase">60-minute total</div>
                </div>

                {{-- Last 24 Hours --}}
                <div class="bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-2xl p-6 border border-indigo-500/20">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="text-indigo-300/80 text-sm font-medium uppercase tracking-wide mb-1">Last 24h</div>
                            <div class="text-5xl font-bold text-white">{{ $module->dashboard_data['sum_rain_24'] ?? '0' }}<span class="text-2xl text-indigo-200/60">mm</span></div>
                        </div>
                        <div class="bg-indigo-500/20 p-3 rounded-xl">
                            <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-xs text-indigo-200/60 uppercase">Daily total</div>
                </div>
            </div>

            {{-- Rain Status Indicator --}}
            <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @php
                            $rainCurrent = $module->dashboard_data['Rain'] ?? 0;
                            $isRaining = $rainCurrent > 0;
                        @endphp
                        <div class="p-3 rounded-lg
                            @if($isRaining) bg-blue-500/20 border border-blue-500/30
                            @else bg-gray-500/20 border border-gray-500/30
                            @endif">
                            <svg class="w-6 h-6
                                @if($isRaining) text-blue-400
                                @else text-gray-400
                                @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-purple-300/80 mb-1">Weather Status</div>
                            <div class="text-xl font-bold
                                @if($isRaining) text-blue-300
                                @else text-gray-300
                                @endif">
                                @if($isRaining)
                                    Raining
                                @else
                                    Dry
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-purple-300/80 mb-1">Precipitation Rate</div>
                        <div class="text-lg font-semibold text-white">
                            @if($rainCurrent > 5)
                                Heavy
                            @elseif($rainCurrent > 2)
                                Moderate
                            @elseif($rainCurrent > 0)
                                Light
                            @else
                                None
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Section --}}
            <div class="grid grid-cols-3 gap-4">
                {{-- Battery --}}
                <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30 hover:border-sky-500/30 transition-colors">
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
                <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30 hover:border-indigo-500/30 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-purple-300/80">Firmware</span>
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ $module->firmware }}</div>
                    <div class="text-xs text-indigo-400/60 mt-1">
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

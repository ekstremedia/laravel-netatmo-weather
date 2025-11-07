{{-- Indoor Module (NAMain) - Main Station --}}
<div class="bg-gradient-to-br from-dark-elevated/90 to-dark-elevated/70 backdrop-blur-xl rounded-3xl shadow-2xl shadow-purple-900/30 border border-dark-border/50 overflow-hidden">

    {{-- Header with Status --}}
    <div class="bg-gradient-to-r from-netatmo-purple/20 via-purple-600/20 to-netatmo-deep/20 px-6 py-4 border-b border-dark-border/50">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-gradient-to-br from-netatmo-purple via-purple-600 to-netatmo-deep p-3 rounded-xl shadow-lg">
                    <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}"
                         alt="Indoor Station"
                         class="w-8 h-8">
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">{{ $module->module_name }}</h3>
                    <p class="text-sm text-purple-200/70 flex items-center mt-1">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Updated @datetime($module->dashboard_data['time_utc'])
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <div class="bg-green-500/20 px-4 py-2 rounded-full border border-green-400/30 backdrop-blur-sm">
                    <span class="text-green-300 text-sm font-semibold flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                        Active
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="p-4 space-y-4">

        {{-- Primary Metrics - Large Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            {{-- Temperature Card --}}
            <div class="bg-gradient-to-br from-orange-500/10 to-red-500/10 rounded-2xl p-4 border border-orange-500/20">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="text-orange-300/80 text-xs font-medium uppercase tracking-wide mb-1.5">Temperature</div>
                        <div class="text-3xl md:text-4xl font-bold text-white leading-none">{{ $module->dashboard_data['Temperature'] }}<span class="text-lg md:text-xl text-orange-200/60">°C</span></div>
                    </div>
                    <div class="bg-orange-500/20 p-2 rounded-xl flex-shrink-0">
                        <svg class="w-6 h-6 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs gap-2">
                    <span class="text-blue-300 flex items-center whitespace-nowrap">
                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        {{ $module->dashboard_data['min_temp'] }}°C
                    </span>
                    <span class="text-orange-200/50 text-[10px]">@time($module->dashboard_data['date_min_temp'])</span>
                    <span class="text-red-300 flex items-center whitespace-nowrap">
                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        {{ $module->dashboard_data['max_temp'] }}°C
                    </span>
                </div>
                <div class="mt-2 pt-2 border-t border-orange-500/20">
                    <span class="text-[10px] text-orange-200/60 uppercase">Trend: </span>
                    <span class="text-xs font-semibold text-orange-200">{{ $module->dashboard_data['temp_trend'] }}</span>
                </div>
            </div>

            {{-- Humidity Card --}}
            <div class="bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-2xl p-4 border border-blue-500/20">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="text-blue-300/80 text-xs font-medium uppercase tracking-wide mb-1.5">Humidity</div>
                        <div class="text-3xl md:text-4xl font-bold text-white leading-none">{{ $module->dashboard_data['Humidity'] }}<span class="text-lg md:text-xl text-blue-200/60">%</span></div>
                    </div>
                    <div class="bg-blue-500/20 p-2 rounded-xl flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-auto">
                    <div class="w-full bg-dark-surface/40 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-400 to-cyan-400 h-2 rounded-full transition-all duration-500" style="width: {{ $module->dashboard_data['Humidity'] }}%"></div>
                    </div>
                    <p class="text-[10px] text-blue-200/60 mt-1.5">
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
            <div class="bg-gradient-to-br from-emerald-500/10 to-green-500/10 rounded-2xl p-4 border border-emerald-500/20">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="flex-1 min-w-0">
                        <div class="text-emerald-300/80 text-xs font-medium uppercase tracking-wide mb-1.5">CO₂ Level</div>
                        <div class="text-3xl md:text-4xl font-bold text-white leading-none">{{ $module->dashboard_data['CO2'] }}<span class="text-base md:text-lg text-emerald-200/60">ppm</span></div>
                    </div>
                    <div class="bg-emerald-500/20 p-2 rounded-xl flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-auto">
                    <p class="text-[10px] text-emerald-200/60 uppercase mb-0.5">Air Quality</p>
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

        {{-- Secondary Metrics --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            {{-- Pressure --}}
            <div class="bg-dark-surface/40 rounded-xl p-3 border border-dark-border/30 hover:border-amber-500/30 transition-colors">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-purple-300/80">Pressure</span>
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="text-xl font-bold text-white leading-none">{{ $module->dashboard_data['Pressure'] }}</div>
                <div class="text-[10px] text-purple-400/60 mt-1">mbar · {{ $module->dashboard_data['pressure_trend'] }}</div>
            </div>

            {{-- Absolute Pressure --}}
            <div class="bg-dark-surface/40 rounded-xl p-3 border border-dark-border/30 hover:border-amber-500/30 transition-colors">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-purple-300/80">Absolute</span>
                    <svg class="w-4 h-4 text-amber-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"/>
                    </svg>
                </div>
                <div class="text-xl font-bold text-white leading-none">{{ $module->dashboard_data['AbsolutePressure'] }}</div>
                <div class="text-[10px] text-purple-400/60 mt-1">mbar</div>
            </div>

            {{-- Noise Level --}}
            <div class="bg-dark-surface/40 rounded-xl p-3 border border-dark-border/30 hover:border-purple-500/30 transition-colors">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-purple-300/80">Noise</span>
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    </svg>
                </div>
                <div class="text-xl font-bold text-white leading-none">{{ $module->dashboard_data['Noise'] }}</div>
                <div class="text-[10px] text-purple-400/60 mt-1">dB</div>
            </div>

            {{-- WiFi Status --}}
            <div class="bg-dark-surface/40 rounded-xl p-3 border border-dark-border/30 hover:border-blue-500/30 transition-colors">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs text-purple-300/80">Wi-Fi</span>
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
                <div class="text-base font-bold text-white leading-none">Signal {{ $module->wifi_status }}</div>
                <div class="text-[10px] text-purple-400/60 mt-1">Firmware {{ $module->firmware }}</div>
            </div>
        </div>
    </div>
</div>

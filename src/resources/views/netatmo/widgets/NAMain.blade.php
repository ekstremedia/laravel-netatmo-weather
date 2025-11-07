<div class="bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-2xl shadow-purple-900/20 border border-dark-border/50 p-6">
    <!-- Module Header -->
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-dark-border/50">
        <div class="flex items-center space-x-4">
            <div class="bg-gradient-to-br from-netatmo-purple via-purple-600 to-netatmo-deep p-3 rounded-xl shadow-lg shadow-purple-900/30">
                <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}"
                     alt="Indoor Module"
                     class="w-8 h-8 brightness-0 invert">
            </div>
            <div>
                <h3 class="text-xl font-bold text-white">{{ $module->module_name }}</h3>
                <p class="text-sm text-purple-300/70">
                    <i class="fas fa-clock mr-1"></i>
                    @datetime($module->dashboard_data['time_utc'])
                </p>
            </div>
        </div>
        <div class="bg-green-900/20 px-3 py-1 rounded-lg border border-green-700/30">
            <span class="text-green-400 text-sm font-medium">
                <i class="fas fa-circle text-xs mr-1"></i>
                Active
            </span>
        </div>
    </div>

    <!-- Temperature Section -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fas fa-thermometer-half text-weather-warm"></i>
                <span class="text-sm text-purple-300">Temperature</span>
            </div>
            <div class="text-2xl font-bold text-white">{{ $module->dashboard_data['Temperature'] }}°C</div>
        </div>
        <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fas fa-temperature-low text-weather-cool"></i>
                <span class="text-sm text-purple-300">Min</span>
            </div>
            <div class="text-lg font-bold text-white">{{ $module->dashboard_data['min_temp'] }}°C</div>
            <div class="text-xs text-purple-400/70">@time($module->dashboard_data['date_min_temp'])</div>
        </div>
        <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fas fa-temperature-high text-red-400"></i>
                <span class="text-sm text-purple-300">Max</span>
            </div>
            <div class="text-lg font-bold text-white">{{ $module->dashboard_data['max_temp'] }}°C</div>
            <div class="text-xs text-purple-400/70">@time($module->dashboard_data['date_max_temp'])</div>
        </div>
        <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fas fa-chart-line text-green-400"></i>
                <span class="text-sm text-purple-300">Trend</span>
            </div>
            <div class="text-lg font-bold text-white">{{ $module->dashboard_data['temp_trend'] }}</div>
        </div>
    </div>

    <!-- Pressure Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fas fa-tachometer-alt text-amber-400"></i>
                <span class="text-sm text-purple-300">Pressure</span>
            </div>
            <div class="text-xl font-bold text-white">{{ $module->dashboard_data['Pressure'] }} <span class="text-sm">mbar</span></div>
        </div>
        <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fas fa-tachometer-alt text-amber-400"></i>
                <span class="text-sm text-purple-300">Absolute</span>
            </div>
            <div class="text-xl font-bold text-white">{{ $module->dashboard_data['AbsolutePressure'] }} <span class="text-sm">mbar</span></div>
        </div>
        <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fas fa-chart-line text-green-400"></i>
                <span class="text-sm text-purple-300">Trend</span>
            </div>
            <div class="text-xl font-bold text-white">{{ $module->dashboard_data['pressure_trend'] }}</div>
        </div>
    </div>

    <!-- Air Quality Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fas fa-tint text-blue-400"></i>
                <span class="text-sm text-purple-300">Humidity</span>
            </div>
            <div class="text-xl font-bold text-white">{{ $module->dashboard_data['Humidity'] }}%</div>
        </div>
        <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fas fa-wind text-green-400"></i>
                <span class="text-sm text-purple-300">CO2</span>
            </div>
            <div class="text-xl font-bold text-white">{{ $module->dashboard_data['CO2'] }} <span class="text-sm">ppm</span></div>
        </div>
        <div class="bg-dark-surface/40 rounded-xl p-4 border border-dark-border/30">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fas fa-volume-up text-purple-400"></i>
                <span class="text-sm text-purple-300">Noise</span>
            </div>
            <div class="text-xl font-bold text-white">{{ $module->dashboard_data['Noise'] }} <span class="text-sm">dB</span></div>
        </div>
    </div>

    <!-- Status Section -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-dark-border/50">
        <div class="text-center">
            <i class="fas fa-wifi text-blue-400 text-lg mb-1"></i>
            <div class="text-xs text-purple-300">Wi-Fi</div>
            <div class="text-sm font-semibold text-white">{{ $module->wifi_status }}</div>
        </div>
        <div class="text-center">
            <i class="fas fa-signal text-purple-400 text-lg mb-1"></i>
            <div class="text-xs text-purple-300">RF Status</div>
            <div class="text-sm font-semibold text-white">{{ $module->rf_status ?? 'N/A' }}</div>
        </div>
        <div class="text-center">
            <i class="fas fa-check-circle text-green-400 text-lg mb-1"></i>
            <div class="text-xs text-purple-300">Reachable</div>
            <div class="text-sm font-semibold text-white">{{ $module->reachable ? 'Yes' : 'No' }}</div>
        </div>
        <div class="text-center">
            <i class="fas fa-code-branch text-purple-400 text-lg mb-1"></i>
            <div class="text-xs text-purple-300">Firmware</div>
            <div class="text-sm font-semibold text-white">{{ $module->firmware }}</div>
        </div>
    </div>
</div>

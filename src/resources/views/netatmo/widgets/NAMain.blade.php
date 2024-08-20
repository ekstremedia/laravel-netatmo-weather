<div class="flex flex-col items-center gap-y-4 p-6 bg-white shadow-lg rounded-lg w-full from-amber-50 to-blue-100 bg-gradient-to-tl">
    <div class="flex items-center justify-center gap-x-4 mb-4">
        <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}" alt="Netatmo Module Logo" width="50"
             class="mr-4">
        <div class="flex flex-col items-center">
            <span class="text-2xl font-bold text-gray-800">{{ $module->module_name }}</span>
            <small class="text-gray-500">Last update: @datetime($module->dashboard_data['time_utc'])</small>
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
        <div class="flex flex-col items-center">
            <i class="fas fa-thermometer-half text-blue-500"></i>
            <span class="font-semibold">Temperature:</span>
            <span class="text-xl">{{ $module->dashboard_data['Temperature'] }}°C</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-temperature-low text-blue-500"></i>
            <span class="font-semibold">Min:</span>
            <span>{{ $module->dashboard_data['min_temp'] }}°C <small>at @time($module->dashboard_data['date_min_temp'])</small></span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-temperature-high text-red-500"></i>
            <span class="font-semibold">Max:</span>
            <span>{{ $module->dashboard_data['max_temp'] }}°C <small>at @time($module->dashboard_data['date_max_temp'])</small></span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-chart-line text-green-500"></i>
            <span class="font-semibold">Trend:</span>
            <span>{{ $module->dashboard_data['temp_trend'] }}</span>
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
        <div class="flex flex-col items-center">
            <i class="fas fa-tachometer-alt text-yellow-500"></i>
            <span class="font-semibold">Pressure:</span>
            <span>{{ $module->dashboard_data['Pressure'] }} mbar</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-tachometer-alt text-yellow-500"></i>
            <span class="font-semibold">Absolute Pressure:</span>
            <span>{{ $module->dashboard_data['AbsolutePressure'] }} mbar</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-chart-line text-green-500"></i>
            <span class="font-semibold">Pressure Trend:</span>
            <span>{{ $module->dashboard_data['pressure_trend'] }}</span>
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
        <div class="flex flex-col items-center">
            <i class="fas fa-tint text-blue-500"></i>
            <span class="font-semibold">Humidity:</span>
            <span>{{ $module->dashboard_data['Humidity'] }}%</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-wind text-green-500"></i>
            <span class="font-semibold">CO2:</span>
            <span>{{ $module->dashboard_data['CO2'] }} ppm</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-volume-up text-red-500"></i>
            <span class="font-semibold">Noise:</span>
            <span>{{ $module->dashboard_data['Noise'] }} dB</span>
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
        <div class="flex flex-col items-center">
            <i class="fas fa-wifi text-blue-500"></i>
            <span class="font-semibold">Wi-Fi Status:</span>
            <span>{{ $module->wifi_status }}</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-battery-half text-green-500"></i>
            <span class="font-semibold">Battery:</span>
            <span>{{ $module->battery_percent ?? 'N/A' }}%</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-wifi text-blue-500"></i>
            <span class="font-semibold">RF Status:</span>
            <span>{{ $module->rf_status ?? 'N/A' }}</span>
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
        <div class="flex flex-col items-center">
            <i class="fas fa-power-off text-red-500"></i>
            <span class="font-semibold">Reachable:</span>
            <span>{{ $module->reachable ? 'Yes' : 'No' }}</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-home text-blue-500"></i>
            <span class="font-semibold">Home:</span>
            <span>{{ $module->home_name }}</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-cogs text-yellow-500"></i>
            <span class="font-semibold">Firmware:</span>
            <span>{{ $module->firmware }}</span>
        </div>
    </div>
</div>

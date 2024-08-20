<div
    class="flex flex-col items-center gap-y-4 p-6 bg-white shadow-lg rounded-lg w-full from-blue-100 to-indigo-50 bg-gradient-to-tl">
    <div class="flex items-center justify-center gap-x-4 mb-4">
        <img src="{{ asset('netatmo-weather/images/icons/rain.svg') }}" alt="Rain Gauge Logo" width="50" class="mr-4">
        <div class="flex flex-col items-center">
            <span class="text-2xl font-bold text-gray-800">{{ $module->module_name }}</span>
            <small class="text-gray-500">Last update: @datetime($module->dashboard_data['time_utc'])</small>
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
        <div class="flex flex-col items-center">
            <i class="fas fa-cloud-rain text-blue-500"></i>
            <span class="font-semibold">Rain:</span>
            <span class="text-xl">{{ $module->dashboard_data['Rain'] }} mm</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-clock text-blue-500"></i>
            <span class="font-semibold">Last Hour:</span>
            <span>{{ $module->dashboard_data['sum_rain_1'] }} mm</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-calendar-day text-blue-500"></i>
            <span class="font-semibold">Last 24 Hours:</span>
            <span>{{ $module->dashboard_data['sum_rain_24'] }} mm</span>
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
        <div class="flex flex-col items-center">
            <i class="fas fa-battery-half text-green-500"></i>
            <span class="font-semibold">Battery:</span>
            <span>{{ $module->battery_percent }}%</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-signal text-blue-500"></i>
            <span class="font-semibold">RF Status:</span>
            <span>{{ $module->rf_status }}</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-cogs text-yellow-500"></i>
            <span class="font-semibold">Firmware:</span>
            <span>{{ $module->firmware }}</span>
        </div>
    </div>

    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
        <div class="flex flex-col items-center">
            <i class="fas fa-power-off text-red-500"></i>
            <span class="font-semibold">Reachable:</span>
            <span>{{ $module->reachable ? 'Yes' : 'No' }}</span>
        </div>
    </div>
</div>

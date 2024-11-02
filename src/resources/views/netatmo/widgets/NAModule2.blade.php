<div class="flex flex-col items-center gap-y-4 p-6 bg-white shadow-lg rounded-lg w-full from-green-100 to-blue-100 bg-gradient-to-tl">
    <div class="flex items-center justify-center gap-x-4 mb-4">
        <img src="{{ asset('netatmo-weather/images/icons/wind.svg') }}" alt="Wind Gauge Logo" width="50"
             class="mr-4">
        <div class="flex flex-col items-center">
            <span class="text-2xl font-bold text-gray-800">{{ $module->module_name }}</span>
            @if($module->dashboard_data)
                <small class="text-gray-500">Last update: @datetime($module->dashboard_data['time_utc'])</small>
            @endif
            <small class="text-gray-500">Last seen: @datetime($module->last_seen)</small>
        </div>
    </div>
    @if($module->dashboard_data)
        <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
            <div class="flex flex-col items-center">
                <i class="fas fa-wind text-blue-500"></i>
                <span class="font-semibold">Wind Strength:</span>
                <span class="text-xl">{{ $module->dashboard_data['WindStrength'] }} km/h</span>
            </div>
            <div class="flex flex-col items-center">
                <i class="fas fa-compass text-blue-500"></i>
                <span class="font-semibold">Wind Angle:</span>
                <span>{{ $module->dashboard_data['WindAngle'] }}°</span>
            </div>
            <div class="flex flex-col items-center">
                <i class="fas fa-tachometer-alt text-red-500"></i>
                <span class="font-semibold">Gust Strength:</span>
                <span>{{ $module->dashboard_data['GustStrength'] }} km/h</span>
            </div>
            <div class="flex flex-col items-center">
                <i class="fas fa-compass text-red-500"></i>
                <span class="font-semibold">Gust Angle:</span>
                <span>{{ $module->dashboard_data['GustAngle'] }}°</span>
            </div>
        </div>

        <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
            <div class="flex flex-col items-center">
                <i class="fas fa-flag text-yellow-500"></i>
                <span class="font-semibold">Max Wind Strength:</span>
                <span>{{ $module->dashboard_data['max_wind_str'] }} km/h</span>
            </div>
            <div class="flex flex-col items-center">
                <i class="fas fa-clock text-blue-500"></i>
                <span class="font-semibold">Max Wind Time:</span>
                <span>@time($module->dashboard_data['date_max_wind_str'])</span>
            </div>
            <div class="flex flex-col items-center">
                <i class="fas fa-signal text-blue-500"></i>
                <span class="font-semibold">RF Status:</span>
                <span>{{ $module->rf_status }}</span>
            </div>
        </div>
    @endif

    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-center text-gray-700">
        <div class="flex flex-col items-center">
            <i class="fas fa-battery-half text-green-500"></i>
            <span class="font-semibold">Battery:</span>
            <span>{{ $module->battery_percent }}%</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-cogs text-yellow-500"></i>
            <span class="font-semibold">Firmware:</span>
            <span>{{ $module->firmware }}</span>
        </div>
        <div class="flex flex-col items-center">
            <i class="fas fa-power-off text-red-500"></i>
            <span class="font-semibold">Reachable:</span>
            <span>{{ $module->reachable ? 'Yes' : 'No' }}</span>
        </div>
    </div>

    @if(!$module->dashboard_data)
        @include('netatmoweather::netatmo.widgets.MissingData')
    @endif
</div>

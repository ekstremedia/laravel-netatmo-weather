<div class="flex flex-col justify-left items-center gap-x-4 w-full gap-y-1">
    <div class="justify-center flex items-center">
        <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}" alt="Netatmo Module Logo" width="50">
        <div>
            {{ $module->module_name }}
        </div>
    </div>
    <div class="flex w-full gap-x-5 justify-center">
        <div class="flex flex-col">
            Temperature:
            {{ $module->latestReading->dashboard_data['Temperature'] }} c
        </div>
        <div class="flex flex-col">
            Min: {{ $module->latestReading->dashboard_data['min_temp'] }} c
        </div>
        <div class="flex flex-col">
            Max: {{ $module->latestReading->dashboard_data['max_temp'] }} c
        </div>
        <div class="flex flex-col">
            Trend:
            {{ $module->latestReading->dashboard_data['temp_trend'] }}
        </div>
    </div>
    <div class="flex w-full gap-x-5  justify-center">
        <div class="flex flex-col">
            Pressure: {{ $module->latestReading->dashboard_data['Pressure'] }} mbar
        </div>
        <div class="flex flex-col">
            Absolute Pressure: {{ $module->latestReading->dashboard_data['AbsolutePressure'] }} mbar
        </div>
        <div class="flex flex-col">
            Trend: {{ $module->latestReading->dashboard_data['pressure_trend'] }}
        </div>
    </div>
    <div class="flex flex-col w-full">
        Humidity:
        {{ $module->latestReading->dashboard_data['Humidity'] }}
    </div>

    <div class="flex flex-col w-full">
        CO2:
        {{ $module->latestReading->dashboard_data['CO2'] }} ppm
    </div>
    <div class="flex flex-col w-full">
        Humidity:
        {{ $module->latestReading->dashboard_data['Humidity'] }} %
    </div>
    <div class="flex flex-col w-full">
        Noise:
        {{ $module->latestReading->dashboard_data['Noise'] }} db
    </div>
</div>

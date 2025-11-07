<div class="space-y-4">
    {{-- Alert --}}
    <div class="flex gap-x-3 items-center border border-yellow-800/30 py-4 px-6 rounded-xl bg-yellow-900/20">
        <div class="bg-yellow-900/30 p-2 rounded-lg">
            <i class="fa fa-triangle-exclamation text-yellow-400 text-lg"></i>
        </div>
        <div class="flex-1">
            <div class="text-yellow-400 font-semibold">No Dashboard Data Available</div>
            <div class="text-yellow-400/70 text-sm">This module is communicating but not sending measurement data</div>
        </div>
    </div>

    {{-- Available Module Information --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        {{-- Module Type --}}
        <div class="bg-dark-surface/40 border border-blue-900/30 rounded-xl p-3">
            <div class="text-blue-300/80 text-xs font-medium uppercase tracking-wide mb-1">Module Type</div>
            <div class="text-white font-semibold">
                {{ match($module->type) {
                    'NAMain' => 'Indoor',
                    'NAModule1' => 'Outdoor',
                    'NAModule2' => 'Wind Gauge',
                    'NAModule3' => 'Rain Gauge',
                    'NAModule4' => 'Indoor',
                    default => $module->type
                } }}
            </div>
        </div>

        {{-- Battery Status --}}
        @if($module->battery_percent)
            <div class="bg-dark-surface/40 border border-green-900/30 rounded-xl p-3">
                <div class="text-green-300/80 text-xs font-medium uppercase tracking-wide mb-1">Battery</div>
                <div class="text-white font-semibold flex items-center">
                    <i class="fas fa-battery-{{ $module->battery_percent > 75 ? 'full' : ($module->battery_percent > 50 ? 'three-quarters' : ($module->battery_percent > 25 ? 'half' : 'quarter')) }} mr-2 {{ $module->battery_percent < 20 ? 'text-red-400' : 'text-green-400' }}"></i>
                    {{ $module->battery_percent }}%
                </div>
            </div>
        @endif

        {{-- RF Signal --}}
        @if($module->rf_status !== null)
            <div class="bg-dark-surface/40 border border-purple-900/30 rounded-xl p-3">
                <div class="text-purple-300/80 text-xs font-medium uppercase tracking-wide mb-1">Signal</div>
                <div class="text-white font-semibold flex items-center">
                    <i class="fas fa-signal mr-2 {{ $module->rf_status < 60 ? 'text-green-400' : ($module->rf_status < 90 ? 'text-yellow-400' : 'text-red-400') }}"></i>
                    {{ $module->rf_status < 60 ? 'Good' : ($module->rf_status < 90 ? 'Fair' : 'Weak') }}
                </div>
            </div>
        @endif

        {{-- Last Seen --}}
        @if($module->last_seen)
            <div class="bg-dark-surface/40 border border-cyan-900/30 rounded-xl p-3">
                <div class="text-cyan-300/80 text-xs font-medium uppercase tracking-wide mb-1">Last Seen</div>
                <div class="text-white font-semibold text-sm">
                    {{ \Carbon\Carbon::createFromTimestamp($module->last_seen)->diffForHumans() }}
                </div>
            </div>
        @endif
    </div>

    {{-- Data Types This Module Supports --}}
    @if(is_array($module->data_type) && count($module->data_type) > 0)
        <div class="bg-dark-surface/40 border border-blue-900/30 rounded-xl p-4">
            <div class="text-blue-300/80 text-xs font-medium uppercase tracking-wide mb-2">
                <i class="fas fa-database mr-1"></i>Supported Data Types
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($module->data_type as $dataType)
                    <span class="inline-flex items-center px-3 py-1.5 bg-blue-500/10 border border-blue-500/30 rounded-lg text-sm text-blue-300">
                        <i class="fas fa-{{ match($dataType) {
                            'Temperature' => 'thermometer-half',
                            'Humidity' => 'droplet',
                            'CO2' => 'smog',
                            'Noise' => 'volume-up',
                            'Pressure' => 'gauge-high',
                            'Rain' => 'cloud-rain',
                            'WindStrength' => 'wind',
                            'WindAngle' => 'compass',
                            'GustStrength' => 'wind',
                            'GustAngle' => 'compass',
                            default => 'circle'
                        } }} mr-2"></i>
                        {{ $dataType }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Troubleshooting Tips --}}
    <div class="bg-blue-900/20 border border-blue-700/30 rounded-xl p-4">
        <div class="text-blue-300 font-semibold mb-2">
            <i class="fas fa-lightbulb mr-1"></i>Troubleshooting
        </div>
        <ul class="text-blue-200/70 text-sm space-y-1 list-disc list-inside">
            <li>Check the module in the Netatmo mobile app</li>
            <li>Try removing and re-adding the module</li>
            @if($module->battery_percent && $module->battery_percent < 20)
                <li class="text-yellow-400">Replace the batteries (currently at {{ $module->battery_percent }}%)</li>
            @endif
            <li>Wait a few minutes and refresh the page</li>
        </ul>
    </div>
</div>

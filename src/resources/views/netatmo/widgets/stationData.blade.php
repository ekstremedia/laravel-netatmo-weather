@php
    $moduleViewMap = [
        'NAMain' => 'netatmoweather::netatmo.widgets.NAMain',
        'NAModule1' => 'netatmoweather::netatmo.widgets.NAModule1',
        'NAModule2' => 'netatmoweather::netatmo.widgets.NAModule2',
        'NAModule3' => 'netatmoweather::netatmo.widgets.NAModule3',
        'NAModule4' => 'netatmoweather::netatmo.widgets.NAModule4',
    ];
@endphp

@include($moduleViewMap[$module->type] ?? 'default.view', ['module' => $module])

{{ $module->module_type }}<br>

@if($module->module_type == 'NAMain')
    @include('netatmoweather::netatmo.widgets.mainModule', ['module' => $module])
@endif

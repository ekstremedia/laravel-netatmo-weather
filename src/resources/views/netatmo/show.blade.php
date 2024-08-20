{{-- src/resources/views/memoryvehicle/show.blade.php --}}
@extends('netatmoweather::layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
            <h2 class="text-xl font-bold mb-4">{{ $weatherStation->station_name }}</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach($weatherStation->modules as $module)
                    @if($module->type === 'NAMain')
                        <div class="col-span-1 lg:col-span-2">
                            @include('netatmoweather::netatmo.widgets.stationData', ['module' => $module])
                        </div>
                    @else
                        <div class="col-span-1">
                            @include('netatmoweather::netatmo.widgets.stationData', ['module' => $module])
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection

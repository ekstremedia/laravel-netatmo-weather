{{-- resources/views/memoryvehicle/fuel/form.blade.php --}}

@extends('netatmoweather::layouts.app')

@section('content')
    <div class="mx-auto container">
        <div class="p-4">
            <h1 class="text-xl">
                <div>
                    @if(isset($vehicle))
                        {{ trans('netatmoweather::messages.vehicle.fuel.edit') }}
                    @else
                        {{ trans('netatmoweather::messages.vehicle.fuel.create') }}
                    @endif
                </div>
                <small>
                    {{ $vehicle->brand }} {{ $vehicle->model }}
                </small>
            </h1>
        </div>
        <div class="mx-4 pt-4 bg-white overflow-hidden shadow-xl rounded-lg p-5 mb-32">
            @include('netatmoweather::netatmo.fuel.partials.form')
        </div>
    </div>
@endsection

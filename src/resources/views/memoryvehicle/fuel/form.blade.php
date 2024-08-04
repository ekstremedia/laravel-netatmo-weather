{{-- resources/views/memoryvehicle/fuel/form.blade.php --}}

@extends('memoryapp::layouts.app')

@section('content')
    <div class="mx-auto container">
        <div class="p-4">
            <h1 class="text-xl">
                <div>
                    @if(isset($vehicle))
                        {{ trans('memoryapp::messages.vehicle.fuel.edit') }}
                    @else
                        {{ trans('memoryapp::messages.vehicle.fuel.create') }}
                    @endif
                </div>
                <small>
                    {{ $vehicle->brand }} {{ $vehicle->model }}
                </small>
            </h1>
        </div>
        <div class="mx-4 pt-4 bg-white overflow-hidden shadow-xl rounded-lg p-5 mb-32">
            @include('memoryapp::memoryvehicle.fuel.partials.form')
        </div>
    </div>
@endsection

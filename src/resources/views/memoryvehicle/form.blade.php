{{-- src/resources/views/memoryvehicle/form.blade.php --}}

@extends('memoryapp::layouts.app')

@section('content')
    <div class="mx-auto container">
        <div class="p-4">
            <h1 class="text-xl">
                @if(isset($vehicle))
                    {{ trans('memoryapp::messages.vehicle.edit') }}
                @else
                    {{ trans('memoryapp::messages.vehicle.create') }}
                @endif
            </h1>
        </div>
        <div class="mx-4 pt-4 bg-white overflow-hidden shadow-xl rounded-lg p-5 mb-32">
            @include('memoryapp::memoryvehicle.partials.form', ['vehicle' => $vehicle ?? null])
        </div>
    </div>
@endsection

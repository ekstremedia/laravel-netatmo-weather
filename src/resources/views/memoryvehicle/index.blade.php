{{-- resources/views/memoryvehicle/index.blade.php --}}

@extends('memoryapp::layouts.app')

@section('content')
    <div class="flex justify-between p-4 w-full">
        <h1 class="text-2xl font-semibold leading-tight">
            <i class="fa fa-car fa-fw"></i> {{ trans('memoryapp::messages.vehicles') }}
        </h1>
        <a href="{{ route('memory.vehicles.create') }}">
            <button class="flex items-center space-x-2 text-sm bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit">
                <i class="fas fa-plus"></i>
                <i class="fa fa-car"></i>
                <span>
                        {{ trans('memoryapp::messages.vehicle.create') }}
                    </span>
            </button>
        </a>
    </div>
    <div class="">
        @include('memoryapp::memoryvehicle.partials.vehicles_list', ['vehicles' => $vehicles])
    </div>
@endsection

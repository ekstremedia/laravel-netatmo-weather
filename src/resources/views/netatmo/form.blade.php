{{-- src/resources/views/netatmo/form.blade.php --}}

@extends('netatmoweather::layouts.app')

@section('content')
    <div class="mx-auto container">
        <div class="p-4">
            <h1 class="text-xl">
                @if(isset($weatherStation))
                    {{ trans('netatmoweather::messages.weatherstation.edit') }}
                @else
                    {{ trans('netatmoweather::messages.weatherstation.add') }}
                @endif
            </h1>
        </div>
        <div class="mx-4 pt-4 bg-white overflow-hidden shadow-xl rounded-lg p-5 mb-32">
            @include('netatmoweather::netatmo.partials.form', ['station' => $weatherStation ?? null])
        </div>
    </div>
@endsection

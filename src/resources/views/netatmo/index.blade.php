{{-- resources/views/netatmo/index.blade.php --}}

@extends('netatmoweather::layouts.app')

@section('content')
    <div class="flex justify-between p-4 w-full">
        <h1 class="text-2xl font-semibold leading-tight flex items-center gap-x-4">
            {{ trans('netatmoweather::messages.weatherstation.weatherstations') }}
            <i class="fa-solid fa-temperature-low"></i>
             ({{ $weatherStations->count() }})
        </h1>
        <a href="{{ route('netatmo.create') }}">
            <button
                class="flex items-center space-x-2 text-sm bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                type="submit">
                <i class="fas fa-circle-plus"></i>
                <span>
                        {{ trans('netatmoweather::messages.weatherstation.add') }}
                    </span>
            </button>
        </a>
    </div>
    <pre class="whitespace-pre-wrap">
{{--            {{ $weatherStations  }}--}}
        </pre>
    <div class="flex flex-col w-full gap-y-2">
        <div class="flex">
            @if($weatherStations->count())
                <div class="flex flex-col gap-y-2 w-full">
                    @foreach($weatherStations as $weatherStation)
                        <div
                            class="bg-green-50 border-l-4 border-green-500 text-red-700 p-4 w-full flex justify-between items-center"
                            role="alert">
                            <p class="font-bold">{{ $weatherStation->station_name }}</p>
                            <div class="flex gap-x-4">
                                <div class="rounded bg-blue-300 px-4 py-1 outline outline-blue-400 text-blue-950">Show
                                </div>
                                <div class="rounded bg-blue-300 px-4 py-1 outline outline-blue-400 text-blue-950">
                                    <a href="{{ route('netatmo.edit', $weatherStation) }}">Edit</a>
                                </div>
                                <div class="rounded bg-blue-300 px-4 py-1 outline outline-blue-400 text-blue-950">Delete
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
        </div>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 flex justify-between items-center w-full"
                 role="alert">
                <p class="font-bold">No weather stations found</p>
                <div class="flex gap-x-4">
                    <div
                        class="rounded bg-blue-300 px-4 py-1 outline outline-blue-400 text-blue-950 flex gap-x-4 items-center">
                        <i class="fas fa-circle-plus text-white"></i>
                        {{ trans('netatmoweather::messages.weatherstation.add') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

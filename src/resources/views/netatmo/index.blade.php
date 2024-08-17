{{-- resources/views/netatmo/index.blade.php --}}

@extends('netatmoweather::layouts.app')

@section('content')
    <div class="flex md:flex-row flex-col justify-between p-4 w-full">
        <h1 class="text-2xl font-semibold leading-tight flex items-center gap-x-4 w-full">
            <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}" alt="Netatmo Station Logo" width="50">
            {{ trans('netatmoweather::messages.weatherstation.weatherstations') }}
            <i class="fa-solid fa-temperature-low"></i>
            ({{ $weatherStations->count() }})
        </h1>
        <div class="flex w-full justify-end">
            <a href="{{ route('netatmo.create') }}" class="">
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
    </div>
    <pre class="whitespace-pre-wrap">
{{--            {{ $weatherStations  }}--}}
        </pre>
    <div class="flex flex-col w-full gap-y-2">
        <div class="flex">
            @if($weatherStations->count())
                <div class="flex flex-col gap-y-2 w-full">
                    {{-- Blade template for listing weather stations --}}
                    @foreach($weatherStations as $weatherstation)
                        <div
                                class="bg-green-50 border-l-4 border-green-500 text-red-700 p-4 w-full flex justify-between items-center"
                                role="alert">
                            <div class="flex items-center gap-x-4">
                                <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}"
                                     alt="Netatmo Station Logo" width="50">
                                <p class="font-bold">{{ $weatherstation->station_name }}</p>
                                @if($weatherstation->token && $weatherstation->token->hasValidToken())
                                    <i class="fa fa-check text-green-700"></i>
                                    <span class="text-green-700">Authenticated</span>
                                @else
                                    <button class="flex items-center space-x-2 text-sm bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        <a href="{{ route('netatmo.authenticate', $weatherstation) }}"
                                           class="btn btn-primary">
                                            Authenticate
                                        </a>
                                    </button>
                                @endif

                            </div>
                            <div class="flex gap-x-4">
                                <a class="rounded bg-blue-300 px-4 py-1 outline outline-blue-400 text-blue-950"
                                   href="{{ route('netatmo.edit', $weatherstation) }}">Edit</a>
                                <div
                                        x-data="{ showConfirm: false }"
                                >
                                    <button class="rounded bg-blue-300 px-4 py-1 outline outline-blue-400 text-blue-950"
                                            @click="showConfirm = true">Delete
                                    </button>

                                    <!-- Confirmation Modal -->
                                    <div
                                            x-show="showConfirm"
                                            x-cloak
                                            class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75">
                                        <div class="bg-white p-6 rounded shadow-xl">
                                            <h2 class="text-lg font-semibold mb-4">Are you sure?</h2>
                                            <p class="mb-6">Do you really want to delete the weather station
                                                <strong>{{ $weatherstation->station_name }}</strong>?</p>
                                            <div class="flex justify-end gap-x-4">
                                                <button @click="showConfirm = false"
                                                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded">Cancel
                                                </button>
                                                <button
                                                        @click="$refs.deleteForm.submit()"
                                                        class="bg-red-600 text-white px-4 py-2 rounded"
                                                >
                                                    Yes, Delete
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Hidden form for delete action -->
                                        <form
                                                x-ref="deleteForm"
                                                method="POST"
                                                action="{{ route('netatmo.destroy', $weatherstation) }}"
                                                class="hidden"
                                        >
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
        </div>
        @else
            <div
                    class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 flex justify-between items-center w-full"
                    role="alert">
                <p class="font-bold">No weather stations found</p>
                <div class="flex gap-x-4">
                    <div
                            class="rounded bg-blue-300 px-4 py-1 outline outline-blue-400 text-blue-950 flex gap-x-4 items-center">
                        <a href="{{ route('netatmo.create') }}">
                            <i class="fas fa-circle-plus text-white"></i>
                            {{ trans('netatmoweather::messages.weatherstation.add') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

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
                    {{-- Blade template for listing weather stations --}}
                    @foreach($weatherStations as $weatherStation)
                        <div
                                class="bg-green-50 border-l-4 border-green-500 text-red-700 p-4 w-full flex justify-between items-center"
                                role="alert">
                            <p class="font-bold">{{ $weatherStation->station_name }}</p>
                            <div class="flex gap-x-4">
                                <div class="rounded bg-blue-300 px-4 py-1 outline outline-blue-400 text-blue-950">
                                    <a href="{{ route('netatmo.edit', $weatherStation) }}">Edit</a>
                                </div>
                                <div
                                        x-data="{ showConfirm: false }"
                                        class="rounded bg-blue-300 px-4 py-1 outline outline-blue-400 text-blue-950 cursor-pointer"
                                >
                                    <span @click="showConfirm = true">Delete</span>

                                    <!-- Confirmation Modal -->
                                    <div
                                            x-show="showConfirm"
                                            x-cloak
                                            class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75">
                                        <div class="bg-white p-6 rounded shadow-xl">
                                            <h2 class="text-lg font-semibold mb-4">Are you sure?</h2>
                                            <p class="mb-6">Do you really want to delete the weather station
                                                <strong>{{ $weatherStation->station_name }}</strong>?</p>
                                            <div class="flex justify-end gap-x-4">
                                                <button @click="showConfirm = false"
                                                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded">No
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
                                                action="{{ route('netatmo.destroy', $weatherStation) }}"
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

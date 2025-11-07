{{-- src/resources/views/netatmo/form.blade.php --}}

@extends('netatmoweather::layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="flex items-center space-x-4 mb-8">
            <div class="bg-gradient-to-br from-netatmo-purple via-purple-600 to-netatmo-deep p-3 rounded-2xl shadow-lg shadow-purple-900/50 ring-2 ring-purple-500/20">
                <i class="fas fa-{{ isset($weatherStation) ? 'edit' : 'plus-circle' }} text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-white">
                    {{ isset($weatherStation) ? trans('netatmoweather::messages.weatherstation.edit') : trans('netatmoweather::messages.weatherstation.add') }}
                </h1>
                <p class="text-sm text-purple-300/80 mt-1">
                    {{ isset($weatherStation) ? 'Update weather station configuration' : 'Configure your Netatmo API credentials' }}
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="max-w-3xl mx-auto">
            <div class="bg-dark-elevated/80 backdrop-blur-xl rounded-2xl shadow-2xl shadow-purple-900/20 border border-dark-border/50 overflow-hidden">
                <div class="p-8">
                    @include('netatmoweather::netatmo.partials.form', ['weatherStation' => $weatherStation ?? null])
                </div>
            </div>
        </div>
    </div>
@endsection

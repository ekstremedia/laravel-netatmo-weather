{{-- resources/views/memoryvehicle/index.blade.php --}}

@extends('netatmoweather::layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Success Message -->
         Laravel Netatmo Weather
        {{ trans('netatmoweather::messages.welcome')  }}
    </div>
@endsection

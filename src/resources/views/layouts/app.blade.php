{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('netatmo-weather.name') }}</title>
    {{-- All assets are self-hosted (published via `vendor:publish --tag=public`)
         so the pages work on hosts with a strict Content-Security-Policy.
         No CDN tags and no inline scripts or styles here — the former Alpine.js
         behaviours live in netatmo-admin.js as data-attribute hooks. --}}
    <link rel="stylesheet" href="{{ asset('netatmo-weather/css/netatmo-weather.css') }}">
    <link rel="stylesheet" href="{{ asset('netatmo-weather/vendor/fontawesome/css/all.min.css') }}">
    <script defer src="{{ asset('netatmo-weather/vendor/chartjs/chart.umd.js') }}"></script>
    <script defer src="{{ asset('netatmo-weather/js/netatmo-charts.js') }}"></script>
    <script defer src="{{ asset('netatmo-weather/js/netatmo-admin.js') }}"></script>
</head>
<body class="bg-dark-bg min-h-screen text-slate-100"
      data-station-uuid="{{ isset($weatherStation) ? $weatherStation->uuid : '' }}">

<div>
    <!-- Navbar -->
    @include('netatmoweather::layouts.navbar')

<div class="flex min-h-screen">
    <!-- Sidebar -->
    @include('netatmoweather::layouts.sidebar')

    <!-- Main Content -->
    <main class="flex-1">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                <div class="bg-green-900/20 border-l-4 border-green-500 rounded-r-xl shadow-lg shadow-green-900/20 backdrop-blur-sm p-4 flex items-start space-x-3"
                     role="alert">
                    <div class="bg-green-600 rounded-full p-1.5 shadow-lg shadow-green-900/30">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-green-300">{{ trans('netatmoweather::messages.general.Success') }}</p>
                        <p class="text-green-400/90">{{ session('success') }}</p>
                    </div>
                    <button data-dismiss-alert class="text-green-400 hover:text-green-300 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                <div class="bg-red-900/20 border-l-4 border-red-500 rounded-r-xl shadow-lg shadow-red-900/20 backdrop-blur-sm p-4 flex items-start space-x-3"
                     role="alert">
                    <div class="bg-red-600 rounded-full p-1.5 shadow-lg shadow-red-900/30">
                        <i class="fas fa-exclamation-circle text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-red-300">Error</p>
                        <p class="text-red-400/90">{{ session('error') }}</p>
                    </div>
                    <button data-dismiss-alert class="text-red-400 hover:text-red-300 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</div>
</body>
</html>

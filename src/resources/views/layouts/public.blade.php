{{-- src/resources/views/layouts/public.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $weatherStation->station_name ?? config('netatmo-weather.name') }} - Weather Data</title>
    {{-- All assets are self-hosted (published via `vendor:publish --tag=public`)
         so the page works on hosts with a strict Content-Security-Policy.
         No CDN tags and no inline scripts or styles here — see netatmo-charts.js. --}}
    <link rel="stylesheet" href="{{ asset('netatmo-weather/css/netatmo-weather.css') }}">
    <link rel="stylesheet" href="{{ asset('netatmo-weather/vendor/fontawesome/css/all.min.css') }}">
    <script defer src="{{ asset('netatmo-weather/vendor/chartjs/chart.umd.js') }}"></script>
    <script defer src="{{ asset('netatmo-weather/js/netatmo-charts.js') }}"></script>
</head>
<body class="bg-dark-bg min-h-screen text-slate-100" data-station-uuid="{{ $weatherStation->uuid }}">
    @yield('content')
</body>
</html>

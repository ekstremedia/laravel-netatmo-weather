{{-- resources/views/layouts/public.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $weatherStation->station_name ?? config('netatmo-weather.name') }} - Weather Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
          integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        html, body {
            height: 100%;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1332;
        }

        ::-webkit-scrollbar-thumb {
            background: #6d28d9;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #8b5cf6;
        }

        /* Fixed background gradient */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(ellipse at top left, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at bottom right, rgba(109, 40, 217, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'netatmo': {
                            'purple': '#8b5cf6',
                            'deep': '#6d28d9',
                            'dark': '#5b21b6',
                        },
                        'dark': {
                            'bg': '#0f0a1f',
                            'surface': '#1a1332',
                            'elevated': '#251b47',
                            'border': '#3d2e6b',
                        },
                        'weather': {
                            'warm': '#f59e0b',
                            'cool': '#06b6d4',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-bg min-h-screen text-slate-100">
    @yield('content')
</body>
</html>

{{-- resources/views/layouts/app.blade.php --}}
        <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('netatmo-weather.name') }}</title>
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
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'netatmo': {
                            'blue': '#0082c3',
                            'light': '#e8f4f8',
                        },
                        'weather': {
                            'warm': '#ff6b35',
                            'cool': '#4ecdc4',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">

<div x-data="{ sidebar_open: false }">
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
                <div class="bg-green-50 border-l-4 border-green-500 rounded-r-xl shadow-lg p-4 flex items-start space-x-3"
                     x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     role="alert">
                    <div class="bg-green-500 rounded-full p-1">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-green-800">{{ trans('netatmoweather::messages.general.Success') }}</p>
                        <p class="text-green-700">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                <div class="bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-lg p-4 flex items-start space-x-3"
                     x-data="{ show: true }"
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     role="alert">
                    <div class="bg-red-500 rounded-full p-1">
                        <i class="fas fa-exclamation-circle text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-red-800">Error</p>
                        <p class="text-red-700">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>

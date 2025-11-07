{{-- resources/views/layout/navbar.blade.php --}}

<nav class="bg-white/80 backdrop-blur-md shadow-lg border-b border-slate-200/50 sticky top-0 z-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo & Title -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-br from-netatmo-blue to-blue-600 p-2.5 rounded-xl shadow-lg">
                        <i class="fa fa-sun text-yellow-300 text-2xl"></i>
                    </div>
                    <div>
                        <a href="{{ route('netatmo.index') }}" class="text-xl font-bold text-slate-800 hover:text-netatmo-blue transition-colors">
                            Netatmo Weather
                        </a>
                        <p class="text-xs text-slate-500 hidden sm:block">Personal Weather Station</p>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <a href="https://dev.netatmo.com/apidocumentation/weather"
                   target="_blank"
                   class="hidden md:flex items-center space-x-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition-all duration-200 group">
                    <img src="{{ asset('netatmo-weather/images/netatmo-logo-vector.svg') }}"
                         alt="Netatmo Logo"
                         class="h-6 opacity-75 group-hover:opacity-100 transition-opacity">
                    <span class="text-sm text-slate-600 group-hover:text-slate-800">API Docs</span>
                    <i class="fas fa-external-link-alt text-xs text-slate-400"></i>
                </a>

                <!-- Mobile menu button -->
                <button @click="sidebar_open = !sidebar_open"
                        class="sm:hidden p-2 rounded-lg hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-netatmo-blue/30 transition-colors">
                    <i class="fa fa-bars text-xl text-slate-700"></i>
                </button>
            </div>
        </div>
    </div>
</nav>


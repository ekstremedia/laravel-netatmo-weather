{{-- resources/views/layout/navbar.blade.php --}}

<nav class="bg-dark-elevated/90 backdrop-blur-xl shadow-2xl border-b border-dark-border/50 sticky top-0 z-50">
    <div class="px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo & Title -->
            <a href="{{ route('netatmo.index') }}" class="flex items-center space-x-3 group">
                <div class="bg-gradient-to-br from-netatmo-purple/20 via-purple-600/20 to-netatmo-deep/20 p-2 rounded-xl border border-purple-500/30 group-hover:border-purple-400/50 transition-all duration-200">
                    <img src="{{ asset('netatmo-weather/images/icons/station.svg') }}"
                         alt="Netatmo Weather"
                         class="w-8 h-8 opacity-90 group-hover:opacity-100 transition-opacity">
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-bold text-white group-hover:text-purple-300 transition-colors">Netatmo Weather</span>
                    <span class="text-xs text-purple-300/60 hidden sm:block">Personal Weather Station</span>
                </div>
            </a>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <!-- Mobile menu button -->
                <button @click="sidebar_open = !sidebar_open"
                        class="sm:hidden p-2 rounded-lg hover:bg-dark-surface border border-dark-border/30 focus:outline-none focus:ring-2 focus:ring-netatmo-purple/50 transition-colors">
                    <i class="fa fa-bars text-xl text-purple-200"></i>
                </button>
            </div>
        </div>
    </div>
</nav>


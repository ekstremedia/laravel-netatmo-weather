{{-- resources/views/layout/navbar.blade.php --}}

<nav class="bg-dark-elevated/90 backdrop-blur-xl shadow-2xl border-b border-dark-border/50 sticky top-0 z-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo & Title -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-br from-netatmo-purple via-purple-600 to-netatmo-deep p-2.5 rounded-xl shadow-lg shadow-purple-900/50 ring-2 ring-purple-500/20">
                        <i class="fa fa-sun text-amber-300 text-2xl drop-shadow-lg"></i>
                    </div>
                    <div>
                        <a href="{{ route('netatmo.index') }}" class="text-xl font-bold text-white hover:text-purple-300 transition-colors">
                            Netatmo Weather
                        </a>
                        <p class="text-xs text-purple-300/70 hidden sm:block">Personal Weather Station</p>
                    </div>
                </div>
            </div>

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


{{-- resources/views/main/partials/modules.blade.php --}}

<div class="space-y-1">
    <a href="{{ route('netatmo.index') }}"
       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group
              {{ Str::startsWith(Route::currentRouteName(), 'netatmo.index') ? 'bg-gradient-to-r from-netatmo-purple to-netatmo-deep text-white shadow-lg shadow-purple-900/50' : 'text-purple-200 hover:bg-dark-surface/60' }}">
        <div class="p-2 rounded-lg {{ Str::startsWith(Route::currentRouteName(), 'netatmo.index') ? 'bg-white/20' : 'bg-dark-surface/60 group-hover:bg-dark-surface' }}">
            <i class="fas fa-home {{ Str::startsWith(Route::currentRouteName(), 'netatmo.index') ? 'text-white' : 'text-purple-300' }}"></i>
        </div>
        <span class="font-medium">Dashboard</span>
    </a>

    <a href="{{ route('netatmo.create') }}"
       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group
              {{ Str::startsWith(Route::currentRouteName(), 'netatmo.create') ? 'bg-gradient-to-r from-netatmo-purple to-netatmo-deep text-white shadow-lg shadow-purple-900/50' : 'text-purple-200 hover:bg-dark-surface/60' }}">
        <div class="p-2 rounded-lg {{ Str::startsWith(Route::currentRouteName(), 'netatmo.create') ? 'bg-white/20' : 'bg-dark-surface/60 group-hover:bg-dark-surface' }}">
            <i class="fas fa-plus-circle {{ Str::startsWith(Route::currentRouteName(), 'netatmo.create') ? 'text-white' : 'text-purple-300' }}"></i>
        </div>
        <span class="font-medium">Add Station</span>
    </a>

    <div class="pt-4 pb-2 px-4">
        <h3 class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Quick Links</h3>
    </div>

    <a href="https://dev.netatmo.com/apidocumentation/weather"
       target="_blank"
       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group text-purple-200 hover:bg-dark-surface/60">
        <div class="p-2 rounded-lg bg-dark-surface/60 group-hover:bg-dark-surface">
            <i class="fas fa-book text-purple-300"></i>
        </div>
        <span class="font-medium">API Documentation</span>
        <i class="fas fa-external-link-alt text-xs ml-auto text-purple-400"></i>
    </a>

    <a href="https://dev.netatmo.com/"
       target="_blank"
       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group text-purple-200 hover:bg-dark-surface/60">
        <div class="p-2 rounded-lg bg-dark-surface/60 group-hover:bg-dark-surface">
            <i class="fas fa-code text-purple-300"></i>
        </div>
        <span class="font-medium">Developer Portal</span>
        <i class="fas fa-external-link-alt text-xs ml-auto text-purple-400"></i>
    </a>
</div>

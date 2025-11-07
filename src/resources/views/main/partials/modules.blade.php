{{-- resources/views/main/partials/modules.blade.php --}}

<div class="space-y-1">
    <a href="{{ route('netatmo.index') }}"
       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group
              {{ Str::startsWith(Route::currentRouteName(), 'netatmo.index') ? 'bg-netatmo-blue text-white shadow-lg' : 'text-slate-600 hover:bg-slate-100' }}">
        <div class="p-2 rounded-lg {{ Str::startsWith(Route::currentRouteName(), 'netatmo.index') ? 'bg-white/20' : 'bg-slate-200 group-hover:bg-slate-300' }}">
            <i class="fas fa-home {{ Str::startsWith(Route::currentRouteName(), 'netatmo.index') ? 'text-white' : 'text-slate-600' }}"></i>
        </div>
        <span class="font-medium">Dashboard</span>
    </a>

    <a href="{{ route('netatmo.create') }}"
       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group
              {{ Str::startsWith(Route::currentRouteName(), 'netatmo.create') ? 'bg-netatmo-blue text-white shadow-lg' : 'text-slate-600 hover:bg-slate-100' }}">
        <div class="p-2 rounded-lg {{ Str::startsWith(Route::currentRouteName(), 'netatmo.create') ? 'bg-white/20' : 'bg-slate-200 group-hover:bg-slate-300' }}">
            <i class="fas fa-plus-circle {{ Str::startsWith(Route::currentRouteName(), 'netatmo.create') ? 'text-white' : 'text-slate-600' }}"></i>
        </div>
        <span class="font-medium">Add Station</span>
    </a>

    <div class="pt-4 pb-2 px-4">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Quick Links</h3>
    </div>

    <a href="https://dev.netatmo.com/apidocumentation/weather"
       target="_blank"
       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group text-slate-600 hover:bg-slate-100">
        <div class="p-2 rounded-lg bg-slate-200 group-hover:bg-slate-300">
            <i class="fas fa-book text-slate-600"></i>
        </div>
        <span class="font-medium">API Documentation</span>
        <i class="fas fa-external-link-alt text-xs ml-auto text-slate-400"></i>
    </a>

    <a href="https://dev.netatmo.com/"
       target="_blank"
       class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group text-slate-600 hover:bg-slate-100">
        <div class="p-2 rounded-lg bg-slate-200 group-hover:bg-slate-300">
            <i class="fas fa-code text-slate-600"></i>
        </div>
        <span class="font-medium">Developer Portal</span>
        <i class="fas fa-external-link-alt text-xs ml-auto text-slate-400"></i>
    </a>
</div>

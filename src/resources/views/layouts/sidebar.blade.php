{{-- resources/views/layout/sidebar.blade.php --}}

<!-- Mobile sidebar overlay -->
<div data-sidebar-overlay
     class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-40 sm:hidden">
</div>

<!-- Sidebar -->
<aside data-sidebar
       class="fixed sm:relative left-0 top-20 sm:top-0 h-[calc(100vh-5rem)] sm:h-auto w-72 sm:w-64 bg-dark-elevated/90 backdrop-blur-xl shadow-2xl sm:shadow-none border-r border-dark-border/50 z-40 transform transition-transform duration-300 ease-in-out -translate-x-full sm:translate-x-0 overflow-y-auto">
    <div class="flex flex-col p-4 space-y-2">
        @include('netatmoweather::main.partials.modules')
    </div>
</aside>

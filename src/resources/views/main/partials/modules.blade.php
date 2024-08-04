{{-- resources/views/main/partials/modules.blade.php --}}

@if(config('memory.modules.vehicle'))
    <a href="{{ route('memory.vehicles.index') }}"
       class="bg-indigo-900/60 p-2 text-indigo-100 flex flex-row h-16 w-32 items-center justify-between space-x-4
                space-x-5 w-full hover:bg-indigo-600 transition duration-300 ease-in-out {{ Str::startsWith(Route::currentRouteName(), 'memory.vehicles.') ? 'font-bold' : '' }}">
        <div>
            <i class="fas fa-car  fa-fw"></i>
            <span>
                        {{ trans('memoryapp::messages.vehicles') }}
                    </span>
        </div>
    </a>
@endif

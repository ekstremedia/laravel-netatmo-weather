{{-- resources/views/layout/navbar.blade.php --}}

<div class="wrapper bg-indigo-600/40 bg-400 flex h-16">
    <div class="sidebar flex flex-row items-center w-full justify-between">
        <div class="logo text-2xl px-7 flex justify-between items-center w-full">
            <div>
                <i class="fa fa-cloud-sun fa-fw"></i>
                <a href="{{ route('netatmo.index') }}">Netatmo Weather</a>
            </div>
            <img src="{{ asset('netatmo-weather/images/netatmo-logo-vector.svg') }}" alt="Description of your image"
                 width="100">
        </div>
        <button @click="sidebar_open = !sidebar_open"
                class="p-2 m-2 text-black hover:text-white focus:outline-none focus:text-white sm:hidden">
            <i class="fa fa-bars"></i>
        </button>
    </div>
</div>


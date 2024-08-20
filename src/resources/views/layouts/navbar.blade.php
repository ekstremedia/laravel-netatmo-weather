{{-- resources/views/layout/navbar.blade.php --}}

<div class="wrapper bg-gradient-to-tr from-blue-400 to-pink-300 bg-400 flex h-16">
    <div class="sidebar flex flex-row items-center w-full justify-between">
        <div class="logo text-2xl px-7 flex justify-between items-center w-full">
            <div>
                <i class="fa fa-sun text-yellow-400 fa-fw"></i>
                <a href="{{ route('netatmo.index') }}">Netatmo Weather</a>
            </div>
            <a href="https://dev.netatmo.com/apidocumentation/weather" target="_blank">
            <img src="{{ asset('netatmo-weather/images/netatmo-logo-vector.svg') }}" alt="Description of your image"
                 width="100">
            </a>
        </div>
        <button @click="sidebar_open = !sidebar_open"
                class="p-2 m-2 text-black hover:text-white focus:outline-none focus:text-white sm:hidden">
            <i class="fa fa-bars"></i>
        </button>
    </div>
</div>


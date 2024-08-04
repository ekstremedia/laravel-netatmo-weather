{{-- resources/views/layout/navbar.blade.php --}}

<div class="wrapper bg-indigo-900/40 flex h-16">
    <div class="sidebar flex flex-row items-center w-full justify-between">
        <div class="logo text-4xl px-7">
            <a href="{{ route('memory.index') }}">Memory</a>
        </div>
        <button @click="sidebar_open = !sidebar_open" class="p-2 m-2 text-black hover:text-white focus:outline-none focus:text-white sm:hidden">
            <i class="fa fa-bars"></i>
        </button>
    </div>
</div>


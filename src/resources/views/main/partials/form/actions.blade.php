<div class="fixed inset-x-0 bottom-0 p-4 border-t bg-slate-300/80 flex items-center space-x-2 justify-end border-t-gray-400">
    <button
        class="bg-green-700 hover:bg-green-700 text-white font-bold py-2 px-8 rounded focus:outline-none focus:shadow-outline"
        type="submit">
        <i class="fa fa-check"></i>
        {{ $buttonText }}
    </button>
    <a href="{{ url()->previous() }}"
       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        {{ trans("memoryapp::messages.general.Cancel") }}
    </a>
</div>

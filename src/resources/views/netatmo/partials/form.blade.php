{{-- src/resources/views/netatmo/partials/form.blade.php --}}
<form method="POST"
      action="{{ isset($weatherStation) ? route('netatmo.update', $weatherStation) : route('netatmo.store') }}"
      class="space-y-6">
    @csrf
    @if(isset($weatherStation))
        @method('PUT')
    @endif

    @foreach ($fields as $field)
        <div class="space-y-2">
            <label class="block text-purple-200 text-sm font-semibold" for="{{ $field['name'] }}">
                {{ trans("netatmoweather::messages.weatherstation." . $field['name']) }}
                @if($field['required'])
                    <span class="text-red-400">*</span>
                @endif
            </label>
            <input
                class="w-full px-4 py-3 bg-dark-surface/60 border border-dark-border/50 rounded-xl text-white placeholder-purple-400/50
                       focus:outline-none focus:ring-2 focus:ring-netatmo-purple/50 focus:border-netatmo-purple/50
                       transition-all duration-200
                       @error($field['name']) border-red-500 ring-2 ring-red-500/20 @enderror"
                id="{{ $field['name'] }}"
                type="{{ $field['type'] }}"
                name="{{ $field['name'] }}"
                value="{{ old($field['name'], isset($weatherStation) ? $weatherStation->{$field['name']} : null) }}"
                placeholder="{{ trans("netatmoweather::messages.weatherstation." . $field['name']) }}"
                {{ $field['required'] ? 'required' : '' }}>

            @error($field['name'])
            <p class="text-red-400 text-sm flex items-center space-x-1">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $message }}</span>
            </p>
            @enderror
        </div>
    @endforeach

    <div class="pt-6 border-t border-dark-border/50 flex items-center justify-end space-x-3">
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center space-x-2 px-6 py-3 bg-dark-surface/60 hover:bg-dark-surface border border-dark-border/50 text-purple-200 font-medium rounded-xl transition-all duration-200">
            <i class="fas fa-times"></i>
            <span>{{ trans("netatmoweather::messages.general.Cancel") }}</span>
        </a>
        <button
            class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-netatmo-purple via-purple-600 to-netatmo-deep hover:from-netatmo-deep hover:to-purple-900 text-white font-semibold rounded-xl shadow-lg shadow-purple-900/50 hover:shadow-xl hover:shadow-purple-800/50 transform hover:-translate-y-0.5 transition-all duration-200 ring-2 ring-purple-500/20"
            type="submit">
            <i class="fas fa-check"></i>
            <span>{{ isset($weatherStation) ? trans("netatmoweather::messages.general.Update") : trans("netatmoweather::messages.general.Save") }}</span>
        </button>
    </div>
</form>

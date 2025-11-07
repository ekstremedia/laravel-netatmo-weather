{{-- src/resources/views/netatmo/partials/form.blade.php --}}
<form method="POST"
      action="{{ isset($weatherStation) ? route('netatmo.update', $weatherStation) : route('netatmo.store') }}"
      class="space-y-6">
    @csrf
    @if(isset($weatherStation))
        @method('PUT')
    @endif

    <!-- Info Box -->
    @if(!isset($weatherStation))
    <div class="bg-blue-900/20 border border-blue-700/30 rounded-xl p-4 mb-6">
        <div class="flex items-start space-x-3">
            <i class="fas fa-info-circle text-blue-400 text-lg mt-1"></i>
            <div>
                <h4 class="text-blue-300 font-semibold mb-1">Get Your Netatmo API Credentials</h4>
                <p class="text-blue-200/70 text-sm mb-2">You'll need to create an app on the Netatmo Developer Portal to get your Client ID and Secret.</p>
                <a href="https://dev.netatmo.com/apps" target="_blank" class="inline-flex items-center space-x-1 text-sm text-blue-300 hover:text-blue-200">
                    <span>Visit Netatmo Developer Portal</span>
                    <i class="fas fa-external-link-alt text-xs"></i>
                </a>
            </div>
        </div>
    </div>
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

    <!-- Public Access Toggle -->
    <div class="space-y-2 pt-6 border-t border-dark-border/50">
        <div class="bg-purple-900/20 border border-purple-700/30 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <input type="checkbox"
                       id="is_public"
                       name="is_public"
                       value="1"
                       {{ old('is_public', isset($weatherStation) && $weatherStation->is_public ? true : false) ? 'checked' : '' }}
                       class="w-5 h-5 mt-0.5 rounded border-purple-500/50 bg-dark-surface/60 text-netatmo-purple focus:ring-2 focus:ring-netatmo-purple/50 focus:ring-offset-0 transition-colors">
                <div class="flex-1">
                    <label for="is_public" class="text-purple-200 font-semibold cursor-pointer block mb-1">
                        <i class="fas fa-share-alt mr-1"></i>
                        Make this station publicly accessible
                    </label>
                    <p class="text-purple-300/70 text-sm">
                        When enabled, anyone with the public link will be able to view this weather station's data without logging in.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="pt-6 flex items-center justify-end space-x-3">
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

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

    <!-- API Settings -->
    <div class="space-y-3 pt-6 border-t border-dark-border/50" x-data="{ apiEnabled: {{ old('api_enabled', isset($weatherStation) && $weatherStation->api_enabled ? 'true' : 'false') }}, generateToken: false }">
        <div class="bg-blue-900/20 border border-blue-700/30 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <input type="checkbox"
                       id="api_enabled"
                       name="api_enabled"
                       value="1"
                       x-model="apiEnabled"
                       {{ old('api_enabled', isset($weatherStation) && $weatherStation->api_enabled ? true : false) ? 'checked' : '' }}
                       class="w-5 h-5 mt-0.5 rounded border-blue-500/50 bg-dark-surface/60 text-netatmo-purple focus:ring-2 focus:ring-netatmo-purple/50 focus:ring-offset-0 transition-colors">
                <div class="flex-1">
                    <label for="api_enabled" class="text-blue-200 font-semibold cursor-pointer block mb-1">
                        <i class="fas fa-code mr-1"></i>
                        Enable API Access
                    </label>
                    <p class="text-blue-300/70 text-sm mb-3">
                        Allow programmatic access to this station's weather data via JSON API endpoints. Perfect for Raspberry Pi, home automation, or custom integrations.
                    </p>

                    <!-- API Token Section -->
                    <div x-show="apiEnabled" x-transition class="mt-3 pt-3 border-t border-blue-700/20">
                        <div class="space-y-2">
                            <label class="text-blue-200 text-sm font-semibold flex items-center space-x-2">
                                <i class="fas fa-key text-xs"></i>
                                <span>API Token (Optional)</span>
                            </label>
                            <p class="text-blue-300/60 text-xs mb-2">
                                Leave empty for public API access, or set a token for authentication via Bearer token.
                            </p>

                            @if(isset($weatherStation) && $weatherStation->api_token)
                                <div class="flex items-center space-x-2">
                                    <button type="button"
                                            @click="generateToken = !generateToken"
                                            class="px-3 py-1.5 bg-blue-700/30 hover:bg-blue-700/50 text-blue-300 text-xs rounded-lg transition-colors">
                                        <i class="fas fa-sync-alt mr-1"></i>
                                        <span x-text="generateToken ? 'Cancel' : 'Regenerate Token'"></span>
                                    </button>
                                    <span class="text-xs text-blue-400/60">Current token is set</span>
                                </div>
                            @else
                                <button type="button"
                                        @click="generateToken = !generateToken"
                                        class="px-3 py-1.5 bg-blue-700/30 hover:bg-blue-700/50 text-blue-300 text-xs rounded-lg transition-colors">
                                    <i class="fas fa-plus mr-1"></i>
                                    <span x-text="generateToken ? 'Cancel' : 'Set API Token'"></span>
                                </button>
                            @endif

                            <div x-show="generateToken" x-transition class="mt-2">
                                <input type="text"
                                       name="api_token"
                                       id="api_token"
                                       value="{{ old('api_token') }}"
                                       placeholder="Enter a secure token or leave empty"
                                       class="w-full px-3 py-2 bg-dark-surface/60 border border-blue-700/30 rounded-lg text-white placeholder-blue-400/40 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                                <p class="text-blue-400/50 text-xs mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Use a long, random string. Minimum 32 characters recommended.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Endpoints Info -->
        @if(isset($weatherStation) && $weatherStation->api_enabled)
        <div x-show="apiEnabled" x-transition class="bg-green-900/20 border border-green-700/30 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <i class="fas fa-check-circle text-green-400 text-lg mt-0.5"></i>
                <div class="flex-1">
                    <h4 class="text-green-300 font-semibold mb-2">API Endpoints</h4>
                    <div class="space-y-1 text-xs font-mono">
                        <div class="flex items-center space-x-2">
                            <span class="text-green-400/70">GET</span>
                            <code class="text-green-300">{{ url(config('netatmo-weather.routes.api.prefix', 'api/netatmo') . '/stations/' . $weatherStation->uuid) }}</code>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-green-400/70">GET</span>
                            <code class="text-green-300">{{ url(config('netatmo-weather.routes.api.prefix', 'api/netatmo') . '/stations/' . $weatherStation->uuid . '/measurements') }}</code>
                        </div>
                    </div>
                    @if($weatherStation->api_token)
                        <p class="text-green-300/60 text-xs mt-2">
                            <i class="fas fa-lock mr-1"></i>
                            Include header: <code class="text-green-300">Authorization: Bearer YOUR_TOKEN</code>
                        </p>
                    @endif
                </div>
            </div>
        </div>
        @endif
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

{{-- src/resources/views/netatmo/partials/form-tabs.blade.php --}}
<form method="POST"
      action="{{ isset($weatherStation) ? route('netatmo.update', $weatherStation) : route('netatmo.store') }}"
      x-data="{ activeTab: 'general' }"
      class="space-y-6">
    @csrf
    @if(isset($weatherStation))
        @method('PUT')
    @endif

    <!-- Tab Navigation -->
    <div class="border-b border-dark-border/50">
        <nav class="flex space-x-1" aria-label="Tabs">
            <button type="button"
                    @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'border-netatmo-purple text-netatmo-purple' : 'border-transparent text-purple-400 hover:text-purple-300 hover:border-purple-500/30'"
                    class="px-4 py-3 border-b-2 font-medium text-sm transition-colors flex items-center space-x-2">
                <i class="fas fa-cog"></i>
                <span>General Settings</span>
            </button>

            @if(isset($weatherStation))
            <button type="button"
                    @click="activeTab = 'sharing'"
                    :class="activeTab === 'sharing' ? 'border-netatmo-purple text-netatmo-purple' : 'border-transparent text-purple-400 hover:text-purple-300 hover:border-purple-500/30'"
                    class="px-4 py-3 border-b-2 font-medium text-sm transition-colors flex items-center space-x-2">
                <i class="fas fa-share-alt"></i>
                <span>Public Sharing</span>
            </button>

            <button type="button"
                    @click="activeTab = 'api'"
                    :class="activeTab === 'api' ? 'border-netatmo-purple text-netatmo-purple' : 'border-transparent text-purple-400 hover:text-purple-300 hover:border-purple-500/30'"
                    class="px-4 py-3 border-b-2 font-medium text-sm transition-colors flex items-center space-x-2">
                <i class="fas fa-code"></i>
                <span>API Access</span>
                @if($weatherStation->api_enabled)
                    <span class="ml-1 inline-flex items-center justify-center w-5 h-5 bg-green-500/20 rounded-full border border-green-500/30">
                        <i class="fa fa-check text-green-400 text-xs"></i>
                    </span>
                @endif
            </button>
            @endif
        </nav>
    </div>

    <!-- Info Box for New Stations -->
    @if(!isset($weatherStation))
    <div class="bg-blue-900/20 border border-blue-700/30 rounded-xl p-4">
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

    <!-- Tab Content -->
    <div class="min-h-[400px]">
        <!-- General Settings Tab -->
        <div x-show="activeTab === 'general'" x-transition class="space-y-6">
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
        </div>

        <!-- Public Sharing Tab -->
        @if(isset($weatherStation))
        <div x-show="activeTab === 'sharing'" x-transition class="space-y-6">
            <div class="bg-purple-900/20 border border-purple-700/30 rounded-xl p-6">
                <div class="flex items-start space-x-4">
                    <input type="checkbox"
                           id="is_public"
                           name="is_public"
                           value="1"
                           {{ old('is_public', $weatherStation->is_public) ? 'checked' : '' }}
                           class="w-6 h-6 mt-0.5 rounded border-purple-500/50 bg-dark-surface/60 text-netatmo-purple focus:ring-2 focus:ring-netatmo-purple/50 focus:ring-offset-0 transition-colors">
                    <div class="flex-1">
                        <label for="is_public" class="text-purple-200 font-bold text-lg cursor-pointer block mb-2">
                            <i class="fas fa-share-alt mr-2"></i>
                            Make this station publicly accessible
                        </label>
                        <p class="text-purple-300/80 mb-4">
                            When enabled, anyone with the public link will be able to view this weather station's data without logging in.
                        </p>

                        @if($weatherStation->is_public)
                        <div class="bg-green-900/20 border border-green-700/30 rounded-lg p-4 mt-4">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-link text-green-400 text-lg"></i>
                                <div class="flex-1">
                                    <h4 class="text-green-300 font-semibold mb-2">Public URL</h4>
                                    <div class="flex items-center space-x-2">
                                        <code class="flex-1 bg-dark-surface/60 px-3 py-2 rounded text-sm text-green-300 border border-green-700/30">
                                            {{ route('netatmo.public', $weatherStation->uuid) }}
                                        </code>
                                        <button type="button"
                                                onclick="navigator.clipboard.writeText('{{ route('netatmo.public', $weatherStation->uuid) }}')"
                                                class="px-3 py-2 bg-green-700/30 hover:bg-green-700/50 text-green-300 rounded transition-colors">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- API Access Tab -->
        @if(isset($weatherStation))
        <div x-show="activeTab === 'api'" x-transition class="space-y-6" x-data="{ apiEnabled: {{ old('api_enabled', $weatherStation->api_enabled) ? 'true' : 'false' }}, generateToken: false }">
            <div class="bg-blue-900/20 border border-blue-700/30 rounded-xl p-6">
                <div class="flex items-start space-x-4">
                    <input type="checkbox"
                           id="api_enabled"
                           name="api_enabled"
                           value="1"
                           x-model="apiEnabled"
                           {{ old('api_enabled', $weatherStation->api_enabled) ? 'checked' : '' }}
                           class="w-6 h-6 mt-0.5 rounded border-blue-500/50 bg-dark-surface/60 text-netatmo-purple focus:ring-2 focus:ring-netatmo-purple/50 focus:ring-offset-0 transition-colors">
                    <div class="flex-1">
                        <label for="api_enabled" class="text-blue-200 font-bold text-lg cursor-pointer block mb-2">
                            <i class="fas fa-code mr-2"></i>
                            Enable API Access
                        </label>
                        <p class="text-blue-300/80 mb-4">
                            Allow programmatic access to this station's weather data via JSON API endpoints. Perfect for Raspberry Pi, home automation, or custom integrations.
                        </p>

                        <!-- API Token Section -->
                        <div x-show="apiEnabled" x-transition class="bg-dark-surface/40 border border-blue-700/20 rounded-lg p-4 space-y-4">
                            <div>
                                <label class="text-blue-200 text-sm font-semibold flex items-center space-x-2 mb-2">
                                    <i class="fas fa-key text-xs"></i>
                                    <span>API Token (Optional - for Bearer Auth)</span>
                                </label>
                                <p class="text-blue-300/70 text-xs mb-3">
                                    Leave empty for public API access, or set a token to require Bearer authentication.
                                </p>

                                @if($weatherStation->api_token)
                                    <div class="flex items-center space-x-2 mb-3">
                                        <span class="inline-flex items-center px-3 py-1.5 bg-green-900/30 border border-green-700/30 rounded-lg text-sm text-green-300">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Token is currently set
                                        </span>
                                        <button type="button"
                                                @click="generateToken = !generateToken"
                                                class="px-3 py-1.5 bg-blue-700/30 hover:bg-blue-700/50 text-blue-300 text-sm rounded-lg transition-colors">
                                            <i class="fas fa-sync-alt mr-1"></i>
                                            <span x-text="generateToken ? 'Cancel' : 'Change Token'"></span>
                                        </button>
                                    </div>
                                @else
                                    <button type="button"
                                            @click="generateToken = !generateToken"
                                            class="px-4 py-2 bg-blue-700/30 hover:bg-blue-700/50 text-blue-300 text-sm rounded-lg transition-colors mb-3">
                                        <i class="fas fa-plus mr-1"></i>
                                        <span x-text="generateToken ? 'Cancel' : 'Set API Token'"></span>
                                    </button>
                                @endif

                                <div x-show="generateToken" x-transition class="space-y-2">
                                    <input type="text"
                                           name="api_token"
                                           id="api_token"
                                           value="{{ old('api_token') }}"
                                           placeholder="Enter a secure token (min 32 characters)"
                                           class="w-full px-4 py-2 bg-dark-surface/60 border border-blue-700/30 rounded-lg text-white placeholder-blue-400/40 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                                    <p class="text-blue-400/60 text-xs">
                                        <i class="fas fa-shield-alt mr-1"></i>
                                        Use a long, random string. Recommended: 32-64 characters.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Endpoints Documentation -->
            <div x-show="apiEnabled" x-transition class="bg-green-900/20 border border-green-700/30 rounded-xl p-6">
                <div class="flex items-start space-x-3 mb-4">
                    <i class="fas fa-book text-green-400 text-xl"></i>
                    <div class="flex-1">
                        <h3 class="text-green-300 font-bold text-lg mb-1">Available API Endpoints</h3>
                        <p class="text-green-300/70 text-sm">Use these endpoints to access your weather data programmatically</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Full Station Data -->
                    <div class="bg-dark-surface/40 border border-green-700/20 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-green-700/30 text-green-300 text-xs font-mono rounded">GET</span>
                                <span class="text-green-200 font-semibold">Full Station Data</span>
                            </div>
                        </div>
                        <code class="block bg-dark-surface/60 px-3 py-2 rounded text-xs text-green-300 font-mono break-all border border-green-700/30">
                            {{ url(config('netatmo-weather.routes.api.prefix', 'api/netatmo') . '/stations/' . $weatherStation->uuid) }}
                        </code>
                        <p class="text-green-300/60 text-xs mt-2">Returns complete station data including all modules, measurements, and location info.</p>
                    </div>

                    <!-- Measurements Only -->
                    <div class="bg-dark-surface/40 border border-green-700/20 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-green-700/30 text-green-300 text-xs font-mono rounded">GET</span>
                                <span class="text-green-200 font-semibold">Measurements Only</span>
                            </div>
                        </div>
                        <code class="block bg-dark-surface/60 px-3 py-2 rounded text-xs text-green-300 font-mono break-all border border-green-700/30">
                            {{ url(config('netatmo-weather.routes.api.prefix', 'api/netatmo') . '/stations/' . $weatherStation->uuid . '/measurements') }}
                        </code>
                        <p class="text-green-300/60 text-xs mt-2">Returns just the latest measurements from all modules. Lighter and faster for polling.</p>
                    </div>

                    <!-- Specific Module -->
                    <div class="bg-dark-surface/40 border border-green-700/20 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-green-700/30 text-green-300 text-xs font-mono rounded">GET</span>
                                <span class="text-green-200 font-semibold">Specific Module</span>
                            </div>
                        </div>
                        <code class="block bg-dark-surface/60 px-3 py-2 rounded text-xs text-green-300 font-mono break-all border border-green-700/30">
                            {{ url(config('netatmo-weather.routes.api.prefix', 'api/netatmo') . '/stations/' . $weatherStation->uuid . '/modules/{moduleId}') }}
                        </code>
                        <p class="text-green-300/60 text-xs mt-2">Returns data for a specific module. Replace {moduleId} with the module's ID.</p>
                    </div>

                    @if($weatherStation->api_token)
                    <!-- Authentication Info -->
                    <div class="bg-yellow-900/20 border border-yellow-700/30 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-lock text-yellow-400"></i>
                            <div class="flex-1">
                                <h4 class="text-yellow-300 font-semibold mb-1">Authentication Required</h4>
                                <p class="text-yellow-300/70 text-xs mb-2">Include this header with all API requests:</p>
                                <code class="block bg-dark-surface/60 px-3 py-2 rounded text-xs text-yellow-300 font-mono border border-yellow-700/30">
                                    Authorization: Bearer YOUR_API_TOKEN
                                </code>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Usage Examples -->
                    <div class="bg-blue-900/20 border border-blue-700/30 rounded-lg p-4">
                        <h4 class="text-blue-300 font-semibold mb-3 flex items-center space-x-2">
                            <i class="fas fa-code"></i>
                            <span>Example Usage</span>
                        </h4>

                        <!-- cURL Example -->
                        <div class="mb-3">
                            <p class="text-blue-300/80 text-xs mb-1">cURL:</p>
                            <code class="block bg-dark-surface/60 px-3 py-2 rounded text-xs text-blue-300 font-mono overflow-x-auto border border-blue-700/30">curl {{ $weatherStation->api_token ? "-H 'Authorization: Bearer YOUR_TOKEN' " : "" }}{{ url(config('netatmo-weather.routes.api.prefix', 'api/netatmo') . '/stations/' . $weatherStation->uuid . '/measurements') }}</code>
                        </div>

                        <!-- Python Example -->
                        <div>
                            <p class="text-blue-300/80 text-xs mb-1">Python:</p>
                            <pre class="bg-dark-surface/60 px-3 py-2 rounded text-xs text-blue-300 overflow-x-auto border border-blue-700/30"><code>import requests
{{ $weatherStation->api_token ? "headers = {'Authorization': 'Bearer YOUR_TOKEN'}\n" : "" }}response = requests.get('{{ url(config('netatmo-weather.routes.api.prefix', 'api/netatmo') . '/stations/' . $weatherStation->uuid . '/measurements') }}'{{ $weatherStation->api_token ? ", headers=headers" : "" }})
data = response.json()</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Form Actions -->
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

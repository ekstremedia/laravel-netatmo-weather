{{-- resources/views/main/index.blade.php --}}

@extends('netatmoweather::layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-4xl mx-auto text-center py-16">
            <!-- Welcome Icon -->
            <div class="bg-gradient-to-br from-purple-900/40 via-purple-800/30 to-netatmo-deep/20 w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-8 border border-purple-500/20 shadow-lg shadow-purple-900/30">
                <i class="fas fa-cloud-sun text-6xl text-purple-400"></i>
            </div>

            <!-- Welcome Text -->
            <h1 class="text-5xl font-bold text-white mb-4">
                Laravel Netatmo Weather
            </h1>
            <p class="text-xl text-purple-200/80 mb-8">
                {{ trans('netatmoweather::messages.welcome') }}
            </p>

            <!-- Quick Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('netatmo.index') }}">
                    <button
                        class="inline-flex items-center space-x-2 px-8 py-4 bg-gradient-to-r from-netatmo-purple via-purple-600 to-netatmo-deep hover:from-netatmo-deep hover:to-purple-900 text-white font-semibold rounded-xl shadow-lg shadow-purple-900/50 hover:shadow-xl hover:shadow-purple-800/50 transform hover:-translate-y-0.5 transition-all duration-200 ring-2 ring-purple-500/20">
                        <i class="fas fa-home"></i>
                        <span>Go to Dashboard</span>
                    </button>
                </a>
                <a href="{{ route('netatmo.create') }}">
                    <button
                        class="inline-flex items-center space-x-2 px-8 py-4 bg-dark-surface/60 hover:bg-dark-surface border border-dark-border/50 text-purple-200 font-medium rounded-xl transition-all duration-200">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Weather Station</span>
                    </button>
                </a>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16">
                <div class="bg-dark-elevated/60 backdrop-blur-sm rounded-2xl p-6 border border-dark-border/50">
                    <div class="bg-purple-900/30 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-2xl text-purple-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Secure OAuth2</h3>
                    <p class="text-purple-200/70 text-sm">Encrypted credentials with automatic token refresh</p>
                </div>

                <div class="bg-dark-elevated/60 backdrop-blur-sm rounded-2xl p-6 border border-dark-border/50">
                    <div class="bg-purple-900/30 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-sync-alt text-2xl text-purple-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Auto Sync</h3>
                    <p class="text-purple-200/70 text-sm">10-minute caching with automatic data updates</p>
                </div>

                <div class="bg-dark-elevated/60 backdrop-blur-sm rounded-2xl p-6 border border-dark-border/50">
                    <div class="bg-purple-900/30 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-th-large text-2xl text-purple-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Multi-Station</h3>
                    <p class="text-purple-200/70 text-sm">Support for multiple weather stations per user</p>
                </div>
            </div>
        </div>
    </div>
@endsection

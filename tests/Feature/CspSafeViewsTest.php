<?php

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Ekstremedia\NetatmoWeather\Models\NetatmoToken;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\get;

/**
 * The rendered pages must work under a strict Content-Security-Policy
 * (script-src 'self', no 'unsafe-eval'): no CDN assets, no inline scripts,
 * no inline event handlers, and no Alpine.js directives (its expression
 * evaluator needs 'unsafe-eval'). These regressions fail silently in the
 * browser — the page renders unstyled instead of erroring — so guard them
 * here.
 */
function assertCspSafeHtml(string $html): void
{
    expect($html)
        ->not->toContain('cdn.tailwindcss.com')
        ->not->toContain('cdn.jsdelivr.net')
        ->not->toContain('cdnjs.cloudflare.com')
        ->not->toContain('alpinejs');

    // Every <script> must have a src (no inline script bodies).
    preg_match_all('/<script\b([^>]*)>/i', $html, $matches);
    foreach ($matches[1] as $attributes) {
        expect($attributes)->toContain('src=');
    }

    // No inline event handlers or Alpine directives on any element.
    expect(preg_match('/\s(onclick|onsubmit|onchange|oninput|onload)=/i', $html))->toBe(0);
    expect(preg_match('/\s(x-data|x-show|x-init|x-cloak|@click)[=\s>]/', $html))->toBe(0);
}

it('renders the public station page without CDN assets, inline scripts, or Alpine', function () {
    $station = NetatmoStation::create([
        'user_id' => 1,
        'station_name' => 'CSP Station',
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'is_public' => true,
        'device_id' => 'test_device_id',
    ]);

    NetatmoToken::create([
        'netatmo_station_id' => $station->id,
        'access_token' => 'valid_access_token',
        'refresh_token' => 'valid_refresh_token',
        'expires_at' => now()->addHour(),
    ]);

    Http::fake([
        config('netatmo-weather.netatmo_api_url').'/getstationsdata' => Http::response([
            'body' => [
                'devices' => [
                    [
                        '_id' => 'test_device_id',
                        'type' => 'NAMain',
                        'module_name' => 'Indoor',
                        'data_type' => ['Temperature', 'Humidity', 'CO2', 'Noise', 'Pressure'],
                        'dashboard_data' => [
                            'Temperature' => 22.5,
                            'Humidity' => 45,
                            'CO2' => 600,
                            'Noise' => 35,
                            'Pressure' => 1013.5,
                            'AbsolutePressure' => 1013.5,
                            'min_temp' => 20.0,
                            'max_temp' => 24.0,
                            'date_min_temp' => now()->subHours(6)->timestamp,
                            'date_max_temp' => now()->subHours(2)->timestamp,
                            'temp_trend' => 'stable',
                            'pressure_trend' => 'stable',
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->app['auth']->forgetGuards();

    $response = get(route('netatmo.public', $station->uuid))->assertOk();

    assertCspSafeHtml($response->getContent());

    // The station uuid must reach netatmo-charts.js via the body data attribute.
    expect($response->getContent())->toContain('data-station-uuid="'.$station->uuid.'"');
});

it('has no CDN references, inline handlers, or Alpine directives in any view source', function () {
    $views = glob(__DIR__.'/../../src/resources/views/{,*/,*/*/,*/*/*/}*.blade.php', GLOB_BRACE);

    expect($views)->not->toBeEmpty();

    foreach ($views as $view) {
        $source = file_get_contents($view);

        expect($source)
            ->not->toContain('cdn.tailwindcss.com')
            ->not->toContain('cdn.jsdelivr.net')
            ->not->toContain('cdnjs.cloudflare.com')
            ->not->toContain('alpinejs')
            ->not->toContain('<script>')
            ->not->toContain('x-data')
            ->not->toContain('x-show')
            ->not->toContain('@click')
            ->not->toContain('onclick=')
            ->not->toContain('onsubmit=');
    }
});

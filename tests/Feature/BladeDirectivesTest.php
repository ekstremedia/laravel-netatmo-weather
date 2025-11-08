<?php

use Illuminate\Support\Facades\Blade;

it('formats datetime with @datetime directive', function () {
    $timestamp = 1704067200; // 2024-01-01 00:00:00 UTC

    $rendered = Blade::render('@datetime(' . $timestamp . ')');

    expect($rendered)->toBeString();
    expect($rendered)->toContain('2024-01-01');
});

it('formats time with @time directive', function () {
    $timestamp = 1704110400; // 2024-01-01 12:00:00 UTC

    $rendered = Blade::render('@time(' . $timestamp . ')');

    expect($rendered)->toBeString();
    expect($rendered)->toMatch('/\d{2}:\d{2}/'); // Matches HH:MM format
});

it('handles string "invalid" as timestamp in @datetime directive', function () {
    // "invalid" string gets converted to 0 timestamp (1970-01-01 00:00:00)
    $rendered = Blade::render('@datetime("invalid")');

    expect($rendered)->toContain('1970-01-01');
});

it('handles string "invalid" as timestamp in @time directive', function () {
    // "invalid" string gets converted to 0 timestamp (00:00)
    $rendered = Blade::render('@time("invalid")');

    expect($rendered)->toBe('00:00');
});

it('handles zero timestamp in @datetime directive', function () {
    $rendered = Blade::render('@datetime(0)');

    expect($rendered)->toContain('1970-01-01');
});

it('handles zero timestamp in @time directive', function () {
    $rendered = Blade::render('@time(0)');

    expect($rendered)->toBe('00:00');
});

it('uses configured timezone for @datetime directive', function () {
    config(['app.timezone' => 'UTC']);

    $timestamp = 1704067200; // 2024-01-01 00:00:00 UTC
    $rendered = Blade::render('@datetime(' . $timestamp . ')');

    expect($rendered)->toContain('2024-01-01');
});

it('uses configured timezone for @time directive', function () {
    config(['app.timezone' => 'UTC']);

    $timestamp = 1704110400; // 2024-01-01 12:00:00 UTC
    $rendered = Blade::render('@time(' . $timestamp . ')');

    expect($rendered)->toMatch('/\d{2}:\d{2}/');
});

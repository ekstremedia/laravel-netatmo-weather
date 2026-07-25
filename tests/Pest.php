<?php

use Ekstremedia\NetatmoWeather\Tests\Support\User;
use Ekstremedia\NetatmoWeather\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');

/**
 * Create a test user for testing.
 */
function createUser(array $attributes = [])
{
    static $userId = 0;
    $userId++;

    return User::create(array_merge([
        'name' => 'Test User '.$userId,
        'email' => 'test'.$userId.'@example.com',
    ], $attributes));
}

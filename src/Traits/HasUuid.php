<?php

namespace Ekstremedia\NetatmoWeather\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot function to auto-generate UUID when creating a new model instance.
     */
    protected static function bootHasUuid(): void
    {
        static::creating(static function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    /**
     * Tells Laravel that the primary key is non-incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Tells Laravel that the primary key is a string (UUID).
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}

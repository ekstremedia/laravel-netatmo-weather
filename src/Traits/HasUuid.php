<?php

namespace Ekstremedia\MemoryApp\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot function to auto-generate UUID when creating a new model instance.
     */
    protected static function bootHasUuid()
    {
        static::creating(static function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }
}

<?php

namespace Ekstremedia\NetatmoWeather\Traits;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    /**
     * Get an attribute from the model.
     *
     * Automatically decrypts encryptable attributes.
     *
     * @param  string  $key
     */
    public function getAttribute($key): mixed
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptable, true) && $value !== null) {
            try {
                return Crypt::decryptString($value);
            } catch (DecryptException $e) {
                logger()->error('Failed to decrypt attribute', [
                    'model' => static::class,
                    'attribute' => $key,
                    'error' => $e->getMessage(),
                ]);

                return null;
            }
        }

        return $value;
    }

    /**
     * Set a given attribute on the model.
     *
     * Automatically encrypts encryptable attributes.
     *
     * @param  string  $key
     * @param  mixed  $value
     */
    public function setAttribute($key, $value): static
    {
        if (in_array($key, $this->encryptable, true) && $value !== null) {
            $value = Crypt::encryptString($value);
        }

        return parent::setAttribute($key, $value);
    }
}

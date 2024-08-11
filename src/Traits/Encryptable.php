<?php

namespace Ekstremedia\NetatmoWeather\Traits;

use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     */
    public function getAttribute($key): mixed
    {
        $value = parent::getAttribute($key); // Call the Model's getAttribute method

        if (in_array($key, $this->encryptable, true)) {
            return Crypt::decryptString($value);
        }

        return $value;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value): static
    {
        if (in_array($key, $this->encryptable, true)) {
            $value = Crypt::encryptString($value);
        }

        return parent::setAttribute($key, $value); // Call the Model's setAttribute method
    }
}

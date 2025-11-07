<?php

namespace Ekstremedia\NetatmoWeather\Policies;

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class NetatmoStationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the weather station.
     */
    public function view(Authenticatable $user, NetatmoStation $station): bool
    {
        return $user->getAuthIdentifier() == $station->user_id;
    }

    /**
     * Determine if the user can update the weather station.
     */
    public function update(Authenticatable $user, NetatmoStation $station): bool
    {
        return $user->getAuthIdentifier() == $station->user_id;
    }

    /**
     * Determine if the user can delete the weather station.
     */
    public function delete(Authenticatable $user, NetatmoStation $station): bool
    {
        return $user->getAuthIdentifier() == $station->user_id;
    }

    /**
     * Determine if the user can authenticate the weather station.
     */
    public function authenticate(Authenticatable $user, NetatmoStation $station): bool
    {
        return $user->getAuthIdentifier() == $station->user_id;
    }
}

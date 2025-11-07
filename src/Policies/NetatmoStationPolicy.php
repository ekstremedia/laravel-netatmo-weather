<?php

namespace Ekstremedia\NetatmoWeather\Policies;

use Ekstremedia\NetatmoWeather\Models\NetatmoStation;
use Illuminate\Auth\Access\HandlesAuthorization;

class NetatmoStationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the weather station.
     */
    public function view($user, NetatmoStation $station): bool
    {
        return $user->id === $station->user_id;
    }

    /**
     * Determine if the user can update the weather station.
     */
    public function update($user, NetatmoStation $station): bool
    {
        return $user->id === $station->user_id;
    }

    /**
     * Determine if the user can delete the weather station.
     */
    public function delete($user, NetatmoStation $station): bool
    {
        return $user->id === $station->user_id;
    }

    /**
     * Determine if the user can authenticate the weather station.
     */
    public function authenticate($user, NetatmoStation $station): bool
    {
        return $user->id === $station->user_id;
    }
}

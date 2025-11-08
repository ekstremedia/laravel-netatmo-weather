<?php

namespace Ekstremedia\NetatmoWeather\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->station_name,
            'device_id' => $this->device_id,
            'modules' => ModuleResource::collection($this->whenLoaded('modules')),
            'location' => $this->getLocationData(),
            'last_updated' => $this->modules()
                ->where('is_active', true)
                ->latest('updated_at')
                ->first()
                ?->updated_at
                ?->toIso8601String(),
            'active_modules_count' => $this->modules()->where('is_active', true)->count(),
        ];
    }

    /**
     * Get location data from the first module that has it.
     */
    protected function getLocationData(): ?array
    {
        $firstModuleWithPlace = $this->modules()
            ->whereNotNull('place')
            ->first();

        if (! $firstModuleWithPlace || ! $firstModuleWithPlace->place) {
            return null;
        }

        $place = $firstModuleWithPlace->place;

        return [
            'altitude' => $place['altitude'] ?? null,
            'city' => $place['city'] ?? null,
            'country' => $place['country'] ?? null,
            'timezone' => $place['timezone'] ?? null,
            'coordinates' => isset($place['location']) ? [
                'latitude' => $place['location'][1] ?? null,
                'longitude' => $place['location'][0] ?? null,
            ] : null,
        ];
    }
}

<?php

namespace Ekstremedia\NetatmoWeather\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->module_id,
            'name' => $this->module_name,
            'type' => $this->getModuleTypeName(),
            'data_types' => $this->data_type,
            'measurements' => $this->dashboard_data,
            'status' => [
                'battery_percent' => $this->battery_percent ? (int) $this->battery_percent : null,
                'rf_status' => $this->getRfStatusText(),
                'reachable' => (bool) $this->reachable,
                'last_seen' => $this->last_seen ? (is_numeric($this->last_seen) ? date('Y-m-d H:i:s', $this->last_seen) : $this->last_seen) : null,
            ],
            'firmware' => $this->firmware,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Get human-readable module type name.
     */
    protected function getModuleTypeName(): string
    {
        return match ($this->type) {
            'NAMain' => 'Indoor Module',
            'NAModule1' => 'Outdoor Module',
            'NAModule2' => 'Wind Gauge',
            'NAModule3' => 'Rain Gauge',
            'NAModule4' => 'Additional Indoor Module',
            default => $this->type,
        };
    }

    /**
     * Get human-readable RF status.
     */
    protected function getRfStatusText(): ?string
    {
        if ($this->rf_status === null) {
            return null;
        }

        return match (true) {
            $this->rf_status < 60 => 'good',
            $this->rf_status < 90 => 'average',
            default => 'weak',
        };
    }
}

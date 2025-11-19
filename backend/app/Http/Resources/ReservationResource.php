<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'parking_spot' => new ParkingSpotResource($this->whenLoaded('parkingSpot')),
            'vehicle_id' => $this->vehicle_id,
            'entry_time' => $this->entry_time?->toIso8601String(),
            'exit_time' => $this->exit_time?->toIso8601String(),
            'expected_exit_time' => $this->expected_exit_time?->toIso8601String(),
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

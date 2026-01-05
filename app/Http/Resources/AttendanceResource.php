<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => optional($this->date)->toDateString(),
            'check_in_at' => optional($this->check_in_at)->toISOString(),
            'check_out_at' => optional($this->check_out_at)->toISOString(),
        ];
    }
}

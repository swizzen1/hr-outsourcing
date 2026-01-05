<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'start_date' => optional($this->start_date)->toDateString(),
            'end_date' => optional($this->end_date)->toDateString(),
            'reason' => $this->reason,
            'type' => $this->type,
            'status' => $this->status,
            'decided_by' => $this->decided_by,
            'decided_at' => optional($this->decided_at)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}

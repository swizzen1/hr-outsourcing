<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsenceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'date' => optional($this->date)->toDateString(),
            'reason' => $this->reason,
            'created_by' => $this->created_by,
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}

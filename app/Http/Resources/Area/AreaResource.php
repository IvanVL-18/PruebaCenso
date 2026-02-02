<?php

namespace App\Http\Resources\Area;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class AreaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'  => Crypt::encryptString($this->id),
            'name' => $this->name,

            'institution_name' => $this->whenLoaded('institution', fn () => $this->institution->name ?? null),

            'institution_eid' => $this->whenLoaded('institution', function () {
                if (!$this->institution?->id) return null;
                return Crypt::encryptString((string) $this->institution->id);
            }),

            'deleted_at' => $this->when($this->deleted_at, $this->deleted_at),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

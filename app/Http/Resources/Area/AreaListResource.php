<?php

namespace App\Http\Resources\Area;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class AreaListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'   => Crypt::encryptString($this->id),
            'name' => $this->name,

            'institution' => [
                'id'   => $this->institution?->id,
                'name' => $this->institution?->name ?? 'Institución deshabilitada',
            ],

            'deleted_at' => $this->deleted_at,
        ];
    }
}

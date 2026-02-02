<?php

namespace App\Http\Resources\IndexForCenso;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class IndexForCensoListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => Crypt::encryptString($this->id),
            
            'censo' => [
                'id'   => $this->censo_id ? Crypt::encryptString($this->censo_id) : null,
                'name' => $this->censo->name ?? null,
            ],

            'index' => [
                'id'   => $this->index_id ? Crypt::encryptString($this->index_id) : null,
                'name' => $this->index->name ?? null,
            ],

            'reference_type' => $this->reference_type,
            
            'reference' => $this->whenLoaded('reference', function () {
                return [
                    'id'   => Crypt::encryptString($this->reference->id),
                    'name' => $this->reference->name ?? null,
                ];
            }),

            'change'     => $this->change,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
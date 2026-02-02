<?php

namespace App\Http\Resources\IndexForCenso;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;

class IndexForCensoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => Crypt::encryptString($this->id),
            'censos_id'      => Crypt::encryptString($this->censos_id),
            'indexs_id'      => Crypt::encryptString($this->indexs_id),
            'reference_type' => $this->reference_type,
            'reference_id'   => Crypt::encryptString($this->reference_id),
            'change'         => $this->change,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'deleted_at'     => $this->when($this->deleted_at, $this->deleted_at),

            // Polimórfica — solo se incluye si está cargada
            'reference' => $this->whenLoaded('reference', function () {
                return [
                    'type' => class_basename($this->reference_type),
                    'id'   => Crypt::encryptString($this->reference->id ?? 0),
                    'name' => $this->reference->name ?? null,
                ];
            }),
        ];
    }
}

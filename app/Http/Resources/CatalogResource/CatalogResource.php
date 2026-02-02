<?php

namespace App\Http\Resources\CatalogResource;
namespace App\Http\Resources\CatalogResource;
use App\Http\Resources\unit\UnitResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;


class CatalogResource extends JsonResource
{
    /**
     * Transform the resource into an 
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
           'id' => Crypt::encryptString($this->id),
            'name' => $this->name,
            'slug' => $this->slug,
            'unit' => new UnitResource($this->whenLoaded('unit')), // Aquí cargamos la relación de 'unit'
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


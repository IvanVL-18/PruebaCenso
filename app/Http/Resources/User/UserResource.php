<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Crypt;


class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => Crypt::encryptString($this->id),
            'name' => $this->name,
            'lastname' => $this->lastname,
            'phone' => $this->phone,
            'movil' => $this->movil,
            'adress' => $this->adress,
            'email' => $this->email,
            
            /* aqui estamos trayendo el rol y la ocupación del usuario */
            'role' => [
            'id' => Crypt::encryptString($this->role->id ?? ''),
            'name' => $this->role->name ?? null,
            ],
            'occupation' => [
                'id' => Crypt::encryptString($this->occupation->id ?? ''),
                'name' => $this->occupation->name ?? null,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->when($this->deleted_at, $this->deleted_at),
        ];
    }
}

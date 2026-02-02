<?php

namespace App\Imports;

use App\Models\Unit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Añadir
use Maatwebsite\Excel\Concerns\SkipsFailures;  // Añadir
use Maatwebsite\Excel\Validators\Failure;

class UnitsImport implements ToCollection, WithValidation, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures; // Esto gestiona automáticamente el array de errores

    public function collection(Collection $collection)
    {
        // En microservicios, el DB::beginTransaction() es mejor dejarlo aquí
        // Pero ten en cuenta que SkipsFailures filtrará las filas inválidas 
        // y aquí solo llegarán las que pasaron la validación.
        foreach ($collection as $row) {
            Unit::create([
                'name' => $row['nombre_de_la_unidad'],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            // El asterisco (*) es vital porque Laravel Excel mapea 
            // cada fila del encabezado como un elemento de un array.
            'nombre_de_la_unidad' => [
                'required',
                'string',
                'min:5',
                'max:65',
                'regex:/^[A-Za-zÁÉÍÓÚÜáéíóúüÑñ\s\'\-\.,\/]+$/u',
                'unique:units,name'
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [       
            'nombre_de_la_unidad.required' => 'El nombre es obligatorio.',
            'nombre_de_la_unidad.string'   => 'El nombre debe ser una cadena de texto.',
            'nombre_de_la_unidad.min'      => 'El nombre debe tener al menos 5 caracteres.',
            'nombre_de_la_unidad.max'      => 'El nombre no puede exceder los 65 caracteres.',
            'nombre_de_la_unidad.regex'    => 'No se permiten números ni caracteres especiales no autorizados.',
            'nombre_de_la_unidad.unique'   => 'El nombre ya está en uso.',   
        ];
    }
}
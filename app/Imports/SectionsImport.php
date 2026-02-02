<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Section;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure; // Añadir
use Maatwebsite\Excel\Concerns\SkipsFailures;  // Añadir
use Maatwebsite\Excel\Validators\Failure;

class SectionsImport implements ToCollection, WithValidation, WithHeadingRow, SkipsOnFailure
{
    use SkipsFailures; // Esto gestiona automáticamente el array de errores

    public function collection(Collection $collection)
    {
        // En microservicios, el DB::beginTransaction() es mejor dejarlo aquí
        // Pero ten en cuenta que SkipsFailures filtrará las filas inválidas 
        // y aquí solo llegarán las que pasaron la validación.
        foreach ($collection as $row) {
            Section::create([
                'name' => $row['nombre_de_la_seccion'],
                'instructions' => $row['instrucciones_de_la_seccion'],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nombre_de_la_seccion' => [
                'required',
                'string',
                'min:5',
                'max:180',
                'regex:/^[A-Za-zÁÉÍÓÚÜáéíóúüÑñ0-9\s\/,\.]+$/u',
            ],
            'instrucciones_de_la_seccion' => [
                'nullable',
                'string',
                'min:10',
                'max:4200',
                'regex:/^[\pL\pM\pN\s\,\.\:\;\-\/\"“”_()º°]+$/u',
                'unique:sections,instructions',
            ],
        ];
    }


    public function customValidationMessages(): array
    {
        return [
            'nombre_de_la_seccion.required' => 'El nombre es obligatorio.',
            'nombre_de_la_seccion.string' => 'El nombre debe ser una cadena de texto.',
            'nombre_de_la_seccion.min' => 'El nombre debe tener al menos 5 caracteres.',
            'nombre_de_la_seccion.max' => 'El nombre no puede exceder los 180 caracteres.',
            'nombre_de_la_seccion.regex' => 'El nombre no puede tener emojis ni otros símbolos.',

            'instrucciones_de_la_seccion.string' => 'La instrucción debe ser una cadena de texto.',
            'instrucciones_de_la_seccion.min' => 'La instrucción debe tener al menos 10 caracteres.',
            'instrucciones_de_la_seccion.max' => 'La instrucción no puede exceder los 4200 caracteres.',
            'instrucciones_de_la_seccion.regex' => 'La instrucción no puede tener emojis ni otros símbolos.',
            'instrucciones_de_la_seccion.unique' => 'La instrucción ya está en uso.',
        ];
    }
}

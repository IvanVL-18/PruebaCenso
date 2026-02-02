<?php

namespace App\Http\Requests\CatalogRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Validation\ValidationException;

class UpdateCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Desencriptar unit_id si viene en la petición.
     */
   protected function prepareForValidation()
    {
        try {
                

                if ($this->has("unit_id") && $this->unit_id) {
                    if(Crypt::decryptString($this->unit_id)){
                        $this->merge([
                        'unit_id' => Crypt::decryptString($this->unit_id)
                        ]);
                    }
                    
                }
            } catch (DecryptException $e) {
                return response()->json([
                    'message' => 'Ocurrió un error al procesar laa solicitud.',
                ], 500); 
            }
    }



    

   public function rules(): array
{
    return [
            'name' => 'required|string|min:5|max:125|regex:/^[\pL\pM\s\'\-\.\,\/]+$/u|unique:catalogs,name', 
            'slug' => 'required|string|max:255|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|unique:catalogs,slug',
            'unit_id' => 'required|exists:units,id|numeric',
    ];
}

public function messages(): array
{
    return [
        'name.required' => 'El nombre del catálogo es obligatorio.',
        'name.string' => 'El nombre debe ser una cadena de texto.',
        'name.min' => 'El nombre debe tener al menos 5 caracteres.',
        'name.max' => 'El nombre no puede exceder los 65 caracteres.',
        'name.regex' => 'El nombre solo puede contener letras, acentos, espacios, comas, puntos, guiones y diagonales (/).',
        'name.unique' => 'El nombre del catálogo ya está en uso.',
        
        'slug.required' => 'El slug del catálogo es obligatorio.',
        'slug.string' => 'El slug debe ser una cadena de texto.',
        'slug.max' => 'El slug no puede exceder los 255 caracteres.',
        'slug.unique' => 'Este slug ya está en uso.',
        
        'unit_id.required' => 'La unidad es obligatoria.',
        'unit_id.exists' => 'La unidad seleccionada no existe.',
        'unit_id.numeric' => 'El identificador de unidad debe ser un número válido.',
    ];
}

}


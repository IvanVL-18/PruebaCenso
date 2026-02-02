<?php

namespace App\Http\Requests\CatalogItemRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Validation\ValidationException;

class UpdateCatalogItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Desencriptar el catalog_id si viene en la petición.
     */
   public function prepareForValidation()
{
    try {
        if ($this->has('catalog_id') && $this->catalog_id) {
            $this->merge([
                'catalog_id' => Crypt::decryptString($this->catalog_id)
            ]);
        }
    } catch (DecryptException $e) {
        return response()->json([
            'message' => 'Ocurrio un error al procesar la solicitud'
        ], 400); 
    }
}


   public function rules(): array
    {
        return [
          'value' => 'required|string|min:5|max:165|regex:/^[\pL\pM\s\'\-\.\,\/]+$/u|unique:items,value',
          'label' => 'required|string|min:5|max:165|regex:/^[^\d\W]*[\pL\pM\s\'\-\.\,\/]+$/u|unique:items,label',
          'catalog_id' => 'required|exists:catalogs,id|numeric',
        ];
}

public function messages(): array
{
    return [
        'value.required' => 'El valor del ítem es obligatorio.',
        'value.string' => 'El valor debe ser una cadena de texto.',
        'value.min' => 'El valor debe tener al menos 5 caracteres.',
        'value.max' => 'El valor no puede exceder los 65 caracteres.',
        'value.regex' => 'El valor solo puede contener letras, números, espacios, comas, puntos, guiones y barras diagonales (/).',
        'value.unique' => 'El valor del ítem ya está en uso.',

        'label.required' => 'La etiqueta del ítem es obligatoria.',
        'label.string' => 'La etiqueta debe ser una cadena de texto.',
        'label.min' => 'La etiqueta debe tener al menos 5 caracteres.',
        'label.max' => 'La etiqueta no puede exceder los 65 caracteres.',
        'label.regex' => 'La etiqueta solo puede contener letras, números, espacios, comas, puntos, guiones y barras diagonales (/).',
        'label.unique' => 'La etiqueta del ítem ya está en uso.',

        'catalog_id.required' => 'El catálogo es obligatorio.',
        'catalog_id.exists' => 'El catálogo seleccionado no existe.',
        'catalog_id.numeric' => 'El id del catálogo debe ser un número válido.',
    ];
}

}
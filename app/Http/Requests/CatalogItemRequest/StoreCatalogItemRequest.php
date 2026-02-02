<?php
namespace App\Http\Requests\CatalogItemRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Validation\ValidationException;

class StoreCatalogItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Desencripta el catalog_id antes  validarlo.
     */
   protected function prepareForValidation()
{
    try {
        if ($this->has('catalog_id') && $this->catalog_id) {
            $decryptedCatalogId = Crypt::decryptString($this->catalog_id);

            $this->merge([
                'catalog_id' => $decryptedCatalogId
            ]);
        }
    } catch (DecryptException $e) {
        throw ValidationException::withMessages([
            'catalog_id' => 'El id del catálogo no está encriptado correctamente o es inválido.'
        ]);
    } catch (\Exception $e) {
        throw ValidationException::withMessages([
            'catalog_id' => 'Ocurrió un error al procesar el catalog_id.'
        ]);
    }
}



 public function rules(): array
{
    return [
     'value' => 'required|string|min:5|max:165|regex:/^[\pL\pM\s\'\-\.\,\/]+$/u',
     'label' => 'required|string|min:5|max:165|regex:/^[^\d\W]*[\pL\pM\s\'\-\.\,\/]+$/u', 
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
        
        'label.required' => 'La etiqueta del ítem es obligatoria.',
        'label.string' => 'La etiqueta debe ser una cadena de texto.',
        'label.min' => 'La etiqueta debe tener al menos 5 caracteres.',
        'label.max' => 'La etiqueta no puede exceder los 65 caracteres.',
        'label.regex' => 'El valor solo puede contener letras, números, espacios, comas, puntos, guiones y barras diagonales (/).',
        
        'catalog_id.required' => 'El catálogo es obligatorio.',
        'catalog_id.exists' => 'El catálogo seleccionado no existe.',
        'catalog_id.numeric' => 'El id del catálogo debe ser un número válido.',
    ];
}

}

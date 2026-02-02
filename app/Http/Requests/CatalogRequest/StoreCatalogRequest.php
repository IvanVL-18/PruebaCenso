<?php

namespace App\Http\Requests\CatalogRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Validation\ValidationException;

class StoreCatalogRequest extends FormRequest
{
    /**
     * Determine  the user is authorized to  this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     */
    protected function prepareForValidation()
    {
        if ($this->has('unit_id') && $this->unit_id) {
            try {
                $decrypted = Crypt::decryptString($this->unit_id);

                if (!is_numeric($decrypted)) {
                    throw new \Exception('El identificador de la unidad no es numérico.');
                }

                $this->merge([
                    'unit_id' => $decrypted
                ]);
            } catch (DecryptException $e) {
                throw ValidationException::withMessages([
                    'unit_id' => ['El id de la unidad no está encriptado correctamente o es inválido.']
                ]);
            } catch (\Exception $e) {
                throw ValidationException::withMessages([
                    'unit_id' => [$e->getMessage() ?: 'Ocurrió un error al procesar el identificador de la unidad.']
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
 public function rules(): array
{
    return [
       'name' => 'required|string|min:5|max:125|regex:/^[\pL\pM\pN\s\'\-\.\,]+$/u|unique:catalogs,name',
        'slug' => 'required|string|max:50|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|unique:catalogs,slug', 
        'unit_id' => 'required|exists:units,id|numeric',
    ];
}

public function messages(): array
{
    return [
        'name.required' => 'El nombre del catálogo es obligatorio.',
        'name.string' => 'El nombre debe ser una cadena de texto.',
        'name.min' => 'El nombre debe tener al menos 5 caracteres.',
        'name.max' => 'El nombre no puede exceder los 45 caracteres.',
        'name.regex' => 'El campo nombre no puede contener caracteres especiales no permitidos.',
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
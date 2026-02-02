<?php

namespace App\Http\Requests\AreaRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class StoreAreaRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Preparar datos antes de validar (desencriptar institution_id)
     */
    protected function prepareForValidation()
    {
        try {
            if ($this->has('institution_id') && $this->institution_id) {
                if (Crypt::decryptString($this->institution_id)) {
                    $this->merge([
                        'institution_id' => Crypt::decryptString($this->institution_id),
                    ]);
                }
            }
        } catch (DecryptException $e) {
            return response()->json([
                'message' => 'Ocurrió un error al procesar la solicitud.',
            ], 500);
        }
    }

    /**
     * Reglas de validación para crear una nueva área.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:5',
                'max:45',
                'regex:/^[\pL\pM\pN\s\'\-\.\,]+$/u',
                'unique:area,name',
            ],

            'institution_id' => [
                'required',
                'integer',
                'exists:institutions,id',
            ],
        ];
    }

    /**
     * Mensajes personalizados de validación.
     */
    public function messages(): array
    {
        return [
            'name.required'   => 'El nombre del área es obligatorio.',
            'name.string'     => 'El nombre del área debe ser una cadena de texto.',
            'name.min'        => 'El nombre del área debe tener al menos 5 caracteres.',
            'name.max'        => 'El nombre del área no puede exceder los 45 caracteres.',
            'name.regex'      => 'El nombre del área no puede contener emojis ni símbolos especiales.',
            'name.unique'     => 'El nombre del área ya está registrado.',

            'institution_id.required' => 'Debe seleccionar una institución.',
            'institution_id.integer'  => 'Ocurrió un error al procesar la solicitud',
            'institution_id.exists'   => 'La institución seleccionada no existe.',
        ];
    }
}

<?php

namespace App\Http\Requests\OccupationRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UpdateOccupationRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation()
    {
        try {
                if ($this->has('institution_id') && $this->institution_id) {
                    if(Crypt::decryptString($this->institution_id)){
                        $this->merge([
                            'institution_id' => Crypt::decryptString($this->institution_id)
                        ]);
                    }     
                }
            } catch (DecryptException $e) {
                return response()->json([
                    'message' => 'Ocurrió un error al procesar la solicitud.',
                ], 500); // Código 500: error interno del servidor
            }
    }

    /**
     * Reglas de validación para actualizar una ocupación existente.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('occupation');
        $decryptedId = null;

        try {
            $decryptedId = Crypt::decryptString($id);
        } catch (\Throwable $e) {
            
        }

        return [
            'name' => [
                'sometimes',
                'string',
                'min:5',
                'max:45',
                'regex:/^[\pL\pM\s\'\-\.\,]+$/u',
                Rule::unique('occupations', 'name')->ignore($decryptedId),
            ],

            'institution_id' => [
                'sometimes',
                'integer',
                'exists:institutions,id',
            ],
        ];
    }

    /**
     * Mensajes personalizados de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string'  => 'El nombre de la ocupación debe ser una cadena de texto.',
            'name.min'     => 'El nombre de la ocupación debe tener al menos 5 caracteres.',
            'name.max'     => 'El nombre de la ocupación no puede exceder los 45 caracteres.',
            'name.regex'   => 'El campo nombre no puede contener emojis ni símbolos especiales.',
            'name.unique'  => 'El nombre de la ocupación ya está en uso.',

            'institution_id.integer' => 'Ocurrió un error al procesar la solicitud',
            'institution_id.exists'  => 'La institución seleccionada no existe.',
        ];
    }
}

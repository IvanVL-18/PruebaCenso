<?php

namespace App\Http\Requests\AreaRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Validation\Rule;

class UpdateAreaRequest extends FormRequest
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
     * Reglas de validación para actualizar un área existente.
     */
    public function rules(): array
    {
        $id = $this->route('area');
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
                'regex:/^[\pL\pM\pN\s\'\-\.\,]+$/u',
                Rule::unique('area', 'name')->ignore($decryptedId),
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
     */
    public function messages(): array
    {
        return [
            'name.string' => 'El nombre del área debe ser una cadena de texto.',
            'name.min'    => 'El nombre del área debe tener al menos 5 caracteres.',
            'name.max'    => 'El nombre del área no puede exceder los 45 caracteres.',
            'name.regex'  => 'El campo nombre no puede contener emojis ni símbolos especiales.',
            'name.unique' => 'El nombre del área ya está en uso.',

            'institution_id.integer' => 'Ocurrió un error al procesar la solicitud',
            'institution_id.exists'  => 'La institución seleccionada no existe.',
        ];
    }
}

<?php

namespace App\Http\Requests\UserRequest;

use Illuminate\Foundation\Http\FormRequest;

class ContentUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        try {
            if ($this->has('role_id') && $this->role_id) {
                if(Crypt::decryptString($this->role_id)){
                    $this->merge([
                        'role_id' => Crypt::decryptString($this->role_id)
                    ]);
                    }     
                }
            
            if ($this->has('institution_id') && $this->institution_id) {
                if(Crypt::decryptString($this->institution_id)){
                    $this->merge([
                        'institution_id' => Crypt::decryptString($this->institution_id)
                    ]);
                }     
            }

            if ($this->has('area_id') && $this->area_id) {
                if(Crypt::decryptString($this->area_id)){
                    $this->merge([
                        'area_id' => Crypt::decryptString($this->area_id)
                    ]);
                }     
            }

            if ($this->has('occupation_id') && $this->occupation_id) {
                if(Crypt::decryptString($this->occupation_id)){
                    $this->merge([
                        'occupation_id' => Crypt::decryptString($this->occupation_id)
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'role_id' => 'sometimes|integer|exists:roles,id',
            'institution_id' => 'sometimes|integer|exists:institutions,id',
            'area_id' => 'sometimes|integer|exists:areas,id',
            'occupation_id' => 'sometimes|integer|exists:occupations,id',
            'content' => 'sometimes|string|max:255',/* solo los valores del content */
        ];
    }

    public function messages(): array
    {
        return [
            //
            'role_id.integer' => 'El ID del rol debe ser un número entero.',
            'role_id.exists' => 'El rol especificado no existe.',
            'institution_id.integer' => 'El ID de la institución debe ser un número entero.',
            'institution_id.exists' => 'La institución especificada no existe.',
            'area_id.integer' => 'El ID del área debe ser un número entero.',
            'area_id.exists' => 'El área especificada no existe.',
            'occupation_id.integer' => 'El ID de la ocupación debe ser un número entero.',
            'occupation_id.exists' => 'La ocupación especificada no existe.',
            'content.string' => 'El contenido debe ser una cadena de texto.',
            'content.max' => 'El contenido no debe exceder los 255 caracteres.',
        ];
    }
}

<?php

namespace App\Http\Requests\PermissionRequest;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para crear un nuevo permiso.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
          'name' => 'required|string|min:4|max:45|regex:/^[\pL\pM\pN\s\'\-\.\,]+$/u|unique:permissions,name',
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
            'name.required' => 'El nombre del permiso es obligatorio.',
            'name.string'   => 'El nombre del permiso debe ser una cadena de texto.',
            'name.min'      => 'El nombre del permiso debe tener al menos 4 caracteres',
            'name.max'      => 'El nombre del permiso no puede exceder los 45 caracteres.',
            'name.regex'    => 'El campo nombre no puede contener emojis ni símbolos especiales.',
            'name.unique'   => 'El nombre del permiso ya está en uso.',
        ];
    }
}

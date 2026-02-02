<?php

namespace App\Http\Requests\UserRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|min:10|max:55|unique:users,email',/* solo acepta correos */
            'password' => [
            'required',
            'string',
            'min:8',
            'max:30',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+=\[{\]};:<>|\/.,?-])[A-Za-z\d!@#$%^&*()_+=\[{\]};:<>|\/.,?-]{8,30}$/'
        ],

        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.string' => 'El campo correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El campo correo electrónico debe ser una dirección de correo electrónico válida.',
            'email.min' => 'El campo correo electrónico debe tener al menos 8 caracteres.',
            'email.max' => 'El campo correo electrónico no debe exceder los 30 caracteres.',
            'email.unique' => 'El correo electrónico ya está en uso.',

            'password.required' => 'El campo contraseña es obligatorio.',
            'password.string' => 'El campo contraseña debe ser una cadena de texto.',
            'password.min' => 'El campo contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'El campo contraseña no debe exceder los 30 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una letra minúscula, un número y un carácter especial.',/* revisar un mensaje más claro */
        ];
    }
}

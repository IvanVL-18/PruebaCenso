<?php

namespace App\Http\Requests\UserRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;


class UpdateUserRequest extends FormRequest
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
                if ($this->has('occupation_id') && $this->occupation_id) {
                    if(Crypt::decryptString($this->occupation_id)){
                        $this->merge([
                        'occupation_id' => Crypt::decryptString($this->occupation_id)
                    ]);
                    }
                    
                }

                if ($this->has('role_id') && $this->role_id) {
                    if(Crypt::decryptString($this->role_id)){
                        $this->merge([
                        'role_id' => Crypt::decryptString($this->role_id)
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
            'name' => 'sometimes|string|min:3|max:45|regex:/^[\pL\pM\pN\s\'\-\.\,]+$/u',
            'lastname' => 'sometimes|string|min:4|max:45|regex:/^[\pL\pM\pN\s\'\-\.\,]+$/u',
            'phone' => 'sometimes|digits:10|unique:users,phone',//solo es para numeros locales no internacionales
            'movil' => 'sometimes|digits:10|unique:users,movil',
            'address' => 'sometimes|string|min:5|max:150|regex:/^[\pL\pM\pN\s\'\-\.\,\#]+$/u',//da más libertad en los caracteres
            'email' => 'sometimes|string|email|max:45|unique:users,email',
            'password' => 'sometimes|string|min:8|max:30|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            'occupation_id' => 'sometimes|integer|exists:occupations,id',
            'role_id' => 'sometimes|integer|exists:roles,id',
        ];
    }

    public function messages()
    {
        return [
            'name.string' => 'El nombre debe ser de tipo texto.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede superar los 45 caracteres.',
            'name.regex' => 'El nombre contiene caracteres no permitidos.',

            'lastname.string' => 'El apellido debe ser de tipo texto.',
            'lastname.min' => 'El apellido debe tener al menos 4 caracteres.',
            'lastname.max' => 'El apellido no puede superar los 45 caracteres.',
            'lastname.regex' => 'El apellido contiene caracteres no permitidos.',

            'phone.digits' => 'El teléfono debe contener exactamente 10 dígitos.',
            'phone.unique' => 'El teléfono ya se encuentra registrado.',

            'movil.digits' => 'El número móvil debe contener exactamente 10 dígitos.',
            'movil.unique' => 'El número móvil ya se encuentra registrado.',

            'address.string' => 'La dirección debe ser de tipo texto.',
            'address.min' => 'La dirección debe tener al menos 5 caracteres.',
            'address.max' => 'La dirección no puede superar los 150 caracteres.',
            'address.regex' => 'La dirección contiene caracteres no permitidos.',

            'email.string' => 'El correo electrónico debe ser válido.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede superar los 45 caracteres.',
            'email.unique' => 'El correo electrónico ya se encuentra registrado.',

            'password.string' => 'La contraseña debe ser de tipo texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede superar los 30 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe incluir al menos una mayúscula, una minúscula, un número y un símbolo especial.',

            /* mensajes de las llaves */
            'occupations_id.exists' => 'La ocupación seleccionada no es válida.',

            'roles_id.exists' => 'El rol seleccionado no es válido.',
        ];
    }
}

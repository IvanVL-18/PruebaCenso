<?php

namespace App\Http\Requests\UserRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:45|regex:/^[\pL\pM\pN\s\'\-\.\,]+$/u',
            'lastname' => 'required|string|min:4|max:45|regex:/^[\pL\pM\pN\s\'\-\.\,]+$/u',
            'phone' => 'required|digits:10|unique:users,phone',//solo es para numeros locales no internacionales
            'movil' => 'required|digits:10|unique:users,movil',
            'address' => 'required|string|min:5|max:150|regex:/^[\pL\pM\pN\s\'\-\.\,\#]+$/u|regex:/\d{0,6}/',

            'email' => 'required|string|email|max:45|unique:users,email',
            'password' => 'required|string|min:8|max:30|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            'occupation_id' => 'required|integer|exists:occupations,id',
            'role_id' => 'required|integer|exists:roles,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser de tipo texto.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede superar los 45 caracteres.',
            'name.regex' => 'El nombre contiene caracteres no permitidos.',

            'lastname.required' => 'El apellido es obligatorio.',
            'lastname.string' => 'El apellido debe ser de tipo texto.',
            'lastname.min' => 'El apellido debe tener al menos 4 caracteres.',
            'lastname.max' => 'El apellido no puede superar los 45 caracteres.',
            'lastname.regex' => 'El apellido contiene caracteres no permitidos.',

            'phone.required' => 'El teléfono es obligatorio.',
            'phone.digits' => 'El teléfono debe contener exactamente 10 dígitos.',
            'phone.unique' => 'El número ya se encuentra registrado.',

            'movil.required' => 'El número móvil es obligatorio.',
            'movil.digits' => 'El número móvil debe contener exactamente 10 dígitos.',
            'movil.unique' => 'El número móvil ya se encuentra registrado.',

            'address.required' => 'La dirección es obligatoria.',
            'address.string' => 'La dirección debe ser de tipo texto.',
            'address.min' => 'La dirección debe tener al menos 5 caracteres.',
            'address.max' => 'La dirección no puede superar los 150 caracteres.',
            'address.regex' => 'La dirección contiene caracteres no permitidos.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser válido.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede superar los 45 caracteres.',
            'email.unique' => 'El correo electrónico ya se encuentra registrado.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser de tipo texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede superar los 30 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe incluir al menos una mayúscula, una minúscula, un número y un símbolo especial.',

            'occupation_id.required' => 'El campo de ocupación es obligatorio.',
            'occupation_id.exists' => 'La ocupación seleccionada no es válida.',

            'role_id.required' => 'El campo de rol es obligatorio.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
        ];
    }

}

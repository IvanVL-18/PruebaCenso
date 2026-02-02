<?php

namespace App\Http\Requests\QuestionRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
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
            'name' => 'required|string|min:20|max:600|unique:questions,name',
            'commentaries' => 'nullable|boolean',
            'instructions' => 'nullable|string|min:20|max:600',
            'type' => 'required|in:empty,radio,check,selector',
            'options' => 'required_unless:type,empty|array|min:1',
            'options.*.name' => 'required|string|min:10|max:60',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'La pregunta es obligatoria.',
            'name.string' => 'La pregunta debe ser una cadena de texto.',
            'name.min' => 'La pregunta debe tener al menos :min caracteres.',
            'name.max' => 'La pregunta no debe exceder de :max caracteres.',
            'name.unique' => 'La pregunta ya existe en el sistema.',

            'commentaries.boolean' => 'El campo comentarios debe ser verdadero o falso.',

            'instructions.string' => 'Las instrucciones deben ser una cadena de texto.',
            'instructions.min' => 'Las instrucciones deben tener al menos :min caracteres.',
            'instructions.max' => 'Las instrucciones no deben exceder de :max caracteres.',

            'type.required' => 'El tipo de pregunta es obligatorio.',
            'type.in' => 'El tipo de pregunta seleccionado no es válido.',

            'options.array' => 'Las opciones deben ser un arreglo.',
            'options.min' => 'Debe proporcionar al menos :min opción.',
            'options.required_unless' => 'Los nombres de las opciones son obligatorios.',
            
            'options.*.name.string' => 'El nombre de la opción debe ser una cadena de texto.',
            'options.*.name.min' => 'El nombre de la opción debe tener al menos :min caracteres.',
            'options.*.name.max' => 'El nombre de la opción no debe exceder de :max caracteres.',
        ];
    }
}

<?php

namespace App\Http\Requests\IndexForCensoRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Validator;

class StoreIndexForCensoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza/descifra entradas antes de validar:
     * - Acepta IDs cifrados o enteros.
     * - Mapea reference_type a FQCN (Section/Unit).
     */
    protected function prepareForValidation(): void
    {
        $map = [
            'section'  => \App\Models\Section::class,
            'sections' => \App\Models\Section::class,
            'unit'     => \App\Models\Unit::class,
            'units'    => \App\Models\Unit::class,
        ];

        $refType = $this->input('reference_type');
        $normType = $map[strtolower((string)$refType)] ?? $refType;

        $this->merge([
            'censo_id'    => $this->decryptIfNeeded($this->input('censo_id')),
            'index_id'    => $this->decryptIfNeeded($this->input('index_id')),
            'reference_id' => $this->decryptIfNeeded($this->input('reference_id')),
            'reference_type' => $normType,
        ]);
    }

    private function decryptIfNeeded($value)
    {
        if (is_null($value) || $value === '') return $value;
        if (is_numeric($value)) return (int) $value;
        try {
            return (int) Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return $value; // Deja que la validación lo marque si no es válido
        }
    }

    public function rules(): array
    {
        return [
            'censo_id'      => ['required','integer','exists:censos,id'],
            'index_id'      => ['required','integer','exists:indexs,id'],
            'reference_type' => ['required','string','in:App\Models\Section,App\Models\Unit,section,sections,unit,units'],
            'reference_id'   => ['required','integer'], // existencia se valida en after()
            'change'         => ['sometimes','integer','in:0,1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $type = $this->input('reference_type');

            // Normaliza por si llega alias en lugar de FQCN
            $map = [
                'section'  => \App\Models\Section::class,
                'sections' => \App\Models\Section::class,
                'unit'     => \App\Models\Unit::class,
                'units'    => \App\Models\Unit::class,
            ];
            $fqcn = $map[strtolower((string)$type)] ?? $type;

            if (!class_exists($fqcn)) {
                $v->errors()->add('reference_type', 'Tipo de referencia inválido.');
                return;
            }

            $id = $this->input('reference_id');
            if (!is_numeric($id)) {
                $v->errors()->add('reference_id', 'El ID de referencia es inválido.');
                return;
            }

            $exists = $fqcn::query()->whereKey($id)->exists();
            if (!$exists) {
                $v->errors()->add('reference_id', 'No existe el recurso relacionado para el tipo indicado.');
            }

            // Asegura que el request final guarde FQCN normalizado
            $this->merge(['reference_type' => $fqcn]);
        });
    }

    public function messages(): array
    {
        return [
            'censo_id.required' => 'El censo es obligatorio.',
            'censo_id.integer'  => 'El ID de censo debe ser numérico.',
            'censo_id.exists'   => 'El censo seleccionado no existe.',
            'index_id.required' => 'El índice es obligatorio.',
            'index_id.integer'  => 'El ID de índice debe ser numérico.',
            'index_id.exists'   => 'El índice seleccionado no existe.',
            'reference_type.required' => 'El tipo de referencia es obligatorio.',
            'reference_type.in'       => 'El tipo de referencia debe ser section/sections o unit/units.',
            'reference_id.required'   => 'El ID de la referencia es obligatorio.',
            'reference_id.integer'    => 'El ID de la referencia debe ser numérico.',
            'change.integer'          => 'El campo change debe ser 0 o 1.',
            'change.in'               => 'El campo change solo permite 0 o 1.',
        ];
    }
}

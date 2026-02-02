<?php

namespace App\Http\Requests\IndexForCensoRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Validator;

class UpdateIndexForCensoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $map = [
            'section'  => \App\Models\Section::class,
            'sections' => \App\Models\Section::class,
            'unit'     => \App\Models\Unit::class,
            'units'    => \App\Models\Unit::class,
        ];

        $refType = $this->input('reference_type');
        $normType = $refType ? ($map[strtolower((string)$refType)] ?? $refType) : $refType;

        $merge = [];

        if ($this->has('censo_id')) {
            $merge['censo_id'] = $this->decryptIfNeeded($this->input('censo_id'));
        }
        if ($this->has('index_id')) {
            $merge['index_id'] = $this->decryptIfNeeded($this->input('index_id'));
        }
        if ($this->has('reference_id')) {
            $merge['reference_id'] = $this->decryptIfNeeded($this->input('reference_id'));
        }
        if ($this->has('reference_type')) {
            $merge['reference_type'] = $normType;
        }

        if ($merge) {
            $this->merge($merge);
        }
    }

    private function decryptIfNeeded($value)
    {
        if (is_null($value) || $value === '') return $value;
        if (is_numeric($value)) return (int) $value;
        try {
            return (int) Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }

    public function rules(): array
    {
        return [
            'censo_id'      => ['sometimes','integer','exists:censos,id'],
            'index_id'      => ['sometimes','integer','exists:indexs,id'],
            'reference_type' => ['sometimes','string','in:App\Models\Section,App\Models\Unit,section,sections,unit,units'],
            'reference_id'   => ['sometimes','integer'], // existencia en after()
            'change'         => ['sometimes','integer','in:0,1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            // Solo validar referencia si llega alguno de los dos campos
            if (!$this->has('reference_type') && !$this->has('reference_id')) {
                return;
            }

            $type = $this->input('reference_type');
            $map = [
                'section'  => \App\Models\Section::class,
                'sections' => \App\Models\Section::class,
                'unit'     => \App\Models\Unit::class,
                'units'    => \App\Models\Unit::class,
            ];
            $fqcn = $map[strtolower((string)$type)] ?? $type;

            if ($this->has('reference_type')) {
                if (!$type || !class_exists($fqcn)) {
                    $v->errors()->add('reference_type', 'Tipo de referencia inválido.');
                    return;
                }
            }

            if ($this->has('reference_id')) {
                $id = $this->input('reference_id');
                if (!is_numeric($id)) {
                    $v->errors()->add('reference_id', 'El ID de referencia es inválido.');
                    return;
                }
                // Si no llegó reference_type en la actualización, tomamos el que ya venía en el modelo vía ruta (se valida en el Controller si se requiere)
                if ($this->has('reference_type')) {
                    $exists = class_exists($fqcn) ? $fqcn::query()->whereKey($id)->exists() : false;
                    if (!$exists) {
                        $v->errors()->add('reference_id', 'No existe el recurso relacionado para el tipo indicado.');
                        return;
                    }
                    // Normaliza para persistencia
                    $this->merge(['reference_type' => $fqcn]);
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'censo_id.integer'  => 'El ID de censo debe ser numérico.',
            'censo_id.exists'   => 'El censo seleccionado no existe.',
            'index_id.integer'  => 'El ID de índice debe ser numérico.',
            'index_id.exists'   => 'El índice seleccionado no existe.',
            'reference_type.in'  => 'El tipo de referencia debe ser section/sections o unit/units.',
            'reference_id.integer' => 'El ID de la referencia debe ser numérico.',
            'change.integer'     => 'El campo change debe ser 0 o 1.',
            'change.in'          => 'El campo change solo permite 0 o 1.',
        ];
    }
}

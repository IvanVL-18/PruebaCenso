<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Index;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithValidation;

class IndexsImport implements ToCollection, WithValidation
{
    public function collection(Collection $collection)
    {
        DB::beginTransaction();

        try {
            foreach ($collection as $row) {
               
                if (!isset($row[0])) continue;

                Index::create([
                    'name' => trim($row[0]),
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            '*.0' => 'required|string|min:3|max:15|unique:indexs,name',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.0.required' => 'El nombre del índice es obligatorio.',
            '*.0.min'      => 'El nombre del índice debe tener al menos 3 caracteres.',
            '*.0.max'      => 'El nombre del índice no debe exceder los 15 caracteres.',
            '*.0.unique'   => 'El nombre del índice ya está registrado.',
        ];
    }
}
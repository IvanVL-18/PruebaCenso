<?php

namespace App\Imports\UnitsImport;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UnitValidation implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        DB::beginTransaction();

        try {
            foreach ($collection as $row) {
                
                /* falta validar */
                Unit::create([
                    'name' => $row[0],
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

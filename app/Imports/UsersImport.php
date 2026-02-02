<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\User;
use App\Models\Role;

class UsersImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        foreach ($collection as $row) 
        {

            $role = Role::where('name', trim($row[7]))->first();

            if (!$role) {
                // puedes saltar el registro, guardar error o asignar uno por defecto
                continue;
            }

            User::create([
                'name' => $row[0],
                'lastname' => $row[1],
                'phone' => $row[2],
                'movil' => $row[3],
                'address' => $row[4],          
                'email' => $row[5],
                'password' => $row[6],
                /* poner pero por palabra que busque cada cosa */
                //'occupation_id' => (int)$row[7], 

                'role_id' => $role->id, 
            ]);
        }
    }
}

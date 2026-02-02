<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Institution;
use App\Models\Censo;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /* mostrar el conteo de total de 
        usuarios
        instituciones
        questionarios
        instituciones centralizadas 
        los questionarios solo el nombre

        Nombre del censo con el total de preguntas ligadas a ese censo falta esta
        */
  
    /* lo de cuestionarios con solo el nombre tiene que usar el metodo de censos específicamente el mètodo index */

    public function count($model){
        switch($model){
            case 'users':
                $total = User::count();
                break;
            case 'institutions':
                $total = Institution::count();
                break;
            case 'censos':
                $total = Censo::count();
                break;
            default:
                return response()->json(['error' => 'Modelo no válido'], 400);
        }

        return response()->json([
            'total' => $total
        ]);
    }


    public function countCentralizedInstitutions()
    {
        $centralized_institutions = Institution::where('typeinst', 'Centralizada')->count();
        $total = Institution::count();

        return response()->json([

            'total_centralized_institutions' => $centralized_institutions,
            'total_institutions' => $total
        ]);
    }

    /* falta el total de preguntas de cada censo */

   
}

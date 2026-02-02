<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Censo;
use App\Http\Resources\Censo\CensoResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\SelectorResource;
use App\Http\Requests\CensoRequest\StoreCensoRequest;
use App\Http\Requests\CensoRequest\UpdateCensoRequest;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class CensoController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SelectorResource::collection(Censo::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCensoRequest $request)//ya esta validado con el StoreCensoRequest
    {
        try {
            Censo::create($request->validated());
            return response()->json([
                'message' => 'Censo creado con exito',
            ], 201);
        } catch (\Throwable $e) {
           return response()->json([
                'message' => 'El censo no se pudo crear',
            ], 400);
        }
    }

    public function test(Request $request)//muestra todos los recursos activos
    {
        /* insertamos la unidad */
       /* foreach para las secciones */
       /* foreach para las preguntas */

    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)//muestra solo recursos activos
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $censo = Censo::withTrashed()->find($decryptedId);

            if (!$censo) {
                return response()->json([
                    'message' => 'Censo no encontrado',
                ], 404);
            }

            return new CensoResource($censo);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Censo no encontrado',
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCensoRequest $request, string $id)//actualiza con las mismas reglas que la creacion y solo recursos activos
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $censo = Censo::find($decryptedId);

            if (!$censo) {
                return response()->json([
                    'message' => 'Censo no encontrado',
                ], 404);
            }

            if (empty($request->validated())) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar.',
                ], 400);
            }

            $censo->update($request->validated());
            return response()->json([
                'message' => 'Censo actualizado con exito',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)//soft delete revisa en todos los registros activos
    {
         try {
            $decryptedId = Crypt::decryptString($id);
            
            $censo = Censo::find($decryptedId);

            if (!$censo) {
                return response()->json([
                    'message' => 'Censo no encontrado',
                ], 404);
            }

            /* desactivamor el recurso */
            $censo->delete();
            //mensaje de exito 
            return response()->json([
                'message' => 'Censo desactivado con exito',
            ], 200);


        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    
    public function restore(string $id)//reactivar un recurso eliminado y solo busca en los eliminados
    {
         try {
            $decryptedId = Crypt::decryptString($id);
            $censo = Censo::onlyTrashed()->find($decryptedId);
 
            if (!$censo) {
                return response()->json([
                    'message' => 'Censo no encontrado',
                ], 404);
            }

            /* reactivamos el recurso */
            $censo->restore();
            //mensaje de exito 
            return response()->json([
                'message' => 'Censo activado con exito',
            ], 200);


        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    public function forceDelete(string $id)//busca solo en los eliminados para eliminar definitivamente
    {
         try {
            $decryptedId = Crypt::decryptString($id);
            $censo = Censo::onlyTrashed()->find($decryptedId);

            if (!$censo) {
                return response()->json([
                    'message' => 'Censo no encontrado',
                ], 404);
            }

            /* eliminamos definitivamente el recurso */
            $censo->forceDelete();
            //mensaje de exito 
            return response()->json([
                'message' => 'Censo eliminado definitivamente con exito',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    //metodo content para el soft delete
    public function content($content)
    {
        try {
            switch ($content) {
                case 'active':
                    return ListResource::collection(Censo::paginate(10));
                    break;
                case 'inactive':
                    return ListResource::collection(Censo::onlyTrashed()->paginate(10));
                    break;
                case 'all':
                    return ListResource::collection(Censo::withTrashed()->paginate(10));
                    break;
                default:
                    return response()->json([
                        'message' => 'Contenido no válido. Use "active", "inactive" o "all".',
                    ], 400);
                }

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }
}

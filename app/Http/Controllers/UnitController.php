<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Http\Resources\Unit\UnitResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\SelectorResource;
use App\Http\Requests\UnitRequest\StoreUnitRequest;
use App\Http\Requests\UnitRequest\UpdateUnitRequest;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\ImportRequest\UploadRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UnitsImport;
use App\Services\ImportServices\ImportService;

class UnitController extends Controller
{

    /* mandar a llamar el service */
    protected ImportService $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SelectorResource::collection(Unit::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnitRequest $request)//ya esta validado con el StoreUnitRequest
    {
        try {
            Unit::create($request->validated());
            return response()->json([
                'message' => 'Unidad creada con exito',
            ], 201);
        } catch (\Throwable $e) {
           return response()->json([
                'message' => 'La unidad no se pudo crear',
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $unit = Unit::withTrashed()->find($decryptedId);

            if (!$unit) {
                return response()->json([
                    'message' => 'Unidad no encontrada',
                ], 404);
            }

            return new UnitResource($unit);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnitRequest $request, string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $unit = Unit::find($decryptedId);

            if (!$unit) {
                return response()->json([
                    'message' => 'Unidad no encontrada',
                ], 404);
            }

            if (empty($request->validated())) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar.',
                ], 400);
            }

            $unit->update($request->validated());

            return response()->json([
                'message' => 'Unidad actualizada con éxito.',
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
    public function destroy(string $id)
    {
         try {
            $decryptedId = Crypt::decryptString($id);
            
            $unit = Unit::find($decryptedId);

            if (!$unit) {
                return response()->json([
                    'message' => 'Unidad no encontrada',
                ], 404);
            }

            /* desactivamor el recurso */
            $unit->delete();
            //mensaje de exito 
            return response()->json([
                'message' => 'Unidad desactivada con exito',
            ], 200);


        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }
  
    public function restore(string $id)
    {
         try {
            $decryptedId = Crypt::decryptString($id);
            $unit = Unit::onlyTrashed()->find($decryptedId);
 
            if (!$unit) {
                return response()->json([
                    'message' => 'Unidad no encontrada',
                ], 404);
            }

            /* reactivamos el recurso */
            $unit->restore();
            //mensaje de exito 
            return response()->json([
                'message' => 'Unidad activada con exito',
            ], 200);


        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    public function forceDelete(string $id)
    {
         try {
            $decryptedId = Crypt::decryptString($id);
            $unit = Unit::onlyTrashed()->find($decryptedId);

            if (!$unit) {
                return response()->json([
                    'message' => 'Unidad no encontrada',
                ], 404);
            }

            /* eliminamos definitivamente el recurso */
            $unit->forceDelete();
            //mensaje de exito 
            return response()->json([
                'message' => 'Unidad eliminada definitivamente con exito',
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
                    return ListResource::collection(Unit::paginate(10));
                    break;
                case 'inactive':
                    return ListResource::collection(Unit::onlyTrashed()->paginate(10));
                    break;
                case 'all':
                    return ListResource::collection(Unit::withTrashed()->paginate(10));
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

    public function import(UploadRequest $request)/* no esta en el request */
    {
        $response =$this->importService->import($request->file('file'), \App\Imports\UnitsImport::class);

        if($response->getData()->success){
            return $response;
        }else{
            return $response;
        }
    }

}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogRequest\StoreCatalogRequest;
use App\Http\Requests\CatalogRequest\UpdateCatalogRequest;
use App\Http\Resources\CatalogResource\CatalogResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\SelectorResource;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Validation\ValidationException;

class CatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SelectorResource::collection(Catalog::all());
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreCatalogRequest $request)
{
    try {
        $validated = $request->validated();

        Catalog::create($validated);

        return response()->json(['message' => 'Catálogo creado exitosamente'], 201);

    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al crear el catálogo'], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            try {
                $decryptedId = Crypt::decryptString($id);
            } catch (DecryptException $e) {
                return response()->json(['message' => 'El id del catálogo no está encriptado o es inválido.'], 400);
            }

            $catalog = Catalog::findOrFail($decryptedId);
            return new CatalogResource($catalog);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Catálogo no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener el catálogo'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
public function update(UpdateCatalogRequest $request, string $id)
{
    try {
        $decryptedId = Crypt::decryptString($id);

        $catalog = Catalog::find($decryptedId);

        abort_if(!$catalog, 404, 'Catálogo no encontrado');

        $catalog->update($request->all());

        return response()->json([
            'message' => 'Catálogo actualizado con éxito'
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Error al procesar la solicitud'
        ], 400);
    }
}



public function forceDelete(string $id)//busca solo en los eliminados para eliminar definitivamente
    {
         try {
            $decryptedId = Crypt::decryptString($id);
            $catalog = Catalog::onlyTrashed()->find($decryptedId);

            if (!$catalog) {
                return response()->json([
                    'message' => 'catalogo no encontrado',
                ], 404);
            }

            /* eliminamos definitivamente el recurso */
            $catalog->forceDelete();
            //mensaje de exito 
            return response()->json([
                'message' => 'catalog eliminado definitivamente con exito',
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
            try {
                $decryptedId = Crypt::decryptString($id);
            } catch (DecryptException $e) {
                return response()->json(['message' => 'El id del catálogo no está encriptado o es inválido.'], 400);
            }

            $catalog = Catalog::onlyTrashed()->findOrFail($decryptedId);
            $catalog->restore();

            return response()->json(['message' => 'Catálogo restaurado exitosamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Catálogo no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al restaurar el catálogo'], 500);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        try {
            try {
                $decryptedId = Crypt::decryptString($id);
            } catch (DecryptException $e) {
                return response()->json(['message' => 'El id del catálogo no está encriptado o es inválido.'], 400);
            }

            $catalog = Catalog::findOrFail($decryptedId);
            $catalog->delete();

            return response()->json(['message' => 'Catálogo eliminado exitosamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Catálogo no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el catálogo'], 500);
        }
    }

    //metodo content para  soft delete
    public function content($content)
    {
        try {
            switch ($content) {
                case 'active':
                    return ListResource::collection(Catalog::paginate(10));
                    break;
                case 'inactive':
                    return ListResource::collection(Catalog::onlyTrashed()->paginate(10));
                    break;
                case 'all':
                    return ListResource::collection(Catalog::withTrashed()->paginate(10));
                    break;
                default:
                    return response()->json([
                        'message' => 'Contenido no válido. Use "active", "inactive" o "all".',
                    ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al procesar la solicitud'], 400);
        }
    }
}

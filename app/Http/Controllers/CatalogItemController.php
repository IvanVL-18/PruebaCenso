<?php
namespace App\Http\Controllers;

use App\Http\Requests\CatalogItemRequest\StoreCatalogItemRequest;
use App\Http\Requests\CatalogItemRequest\UpdateCatalogItemRequest;
use App\Http\Resources\CatalogItemResource\CatalogItemResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\SelectorResource;
use App\Models\CatalogItem;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;

class CatalogItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SelectorResource::collection(CatalogItem::all());
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreCatalogItemRequest $request)
{
    try {
        $validated = $request->validated();
        $item = CatalogItem::create($validated);

        return response()->json(['message' => 'Ítem creado exitosamente'], 201); 
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al crear el ítem del catálogo'], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $item = CatalogItem::findOrFail($decryptedId);
            return new CatalogItemResource($item);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ítem no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener el ítem'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
 public function update(UpdateCatalogItemRequest $request, string $id)
{
    try {
        $decryptedId = Crypt::decryptString($id); 
        $item = CatalogItem::findOrFail($decryptedId);

        $validated = $request->validated();  

        $item->update($validated);

        return response()->json(['message' => 'Ítem actualizado exitosamente'], 200); 
    } catch (ModelNotFoundException $e) {
        return response()->json(['message' => 'Ítem no encontrado'], 404);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al actualizar el ítem'], 500);
    }
}



 public function restore(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $catalogItem = CatalogItem::onlyTrashed()->find($decryptedId);

            if (!$catalogItem) {
                return response()->json([
                    'message' => 'Ítem no encontrado en los eliminados',
                ], 404);
            }

            $catalogItem->restore();
            return response()->json([
                'message' => 'Ítem restaurado exitosamente',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    /**
     * Eliminar un recurso de manera definitiva (Force Delete).
     */
    public function forceDelete(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $catalogItem = CatalogItem::onlyTrashed()->find($decryptedId);

            if (!$catalogItem) {
                return response()->json([
                    'message' => 'Ítem no encontrado en los eliminados',
                ], 404);
            }

            $catalogItem->forceDelete();
            return response()->json([
                'message' => 'Ítem eliminado definitivamente con éxito',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    /**
     * Filtrar recursos dependiendo del estado (activos, inactivos, todos).
     */
    public function content($content)
    {
        try {
            switch ($content) {
                case 'active':
                    return ListResource::collection(CatalogItem::paginate(10));
                    break;
                case 'inactive':
                    return ListResource::collection(CatalogItem::onlyTrashed()->paginate(10));
                    break;
                case 'all':
                    return ListResource::collection(CatalogItem::withTrashed()->paginate(10));
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

    /**
     * Remove the specified  from storage (soft delete).
     */
    public function destroy(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $item = CatalogItem::findOrFail($decryptedId);
            $item->delete();
            return response()->json(['message' => 'Ítem eliminado exitosamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ítem no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el ítem'], 500);
        }
    }
}

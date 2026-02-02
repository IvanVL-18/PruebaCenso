<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Http\Resources\Area\AreaResource;
use App\Http\Resources\Area\AreaListResource;
use App\Http\Requests\AreaRequest\StoreAreaRequest;
use App\Http\Requests\AreaRequest\UpdateAreaRequest;
use Illuminate\Support\Facades\Crypt;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return AreaListResource::collection(
            Area::with(['institution' => function ($query) {
                $query->withTrashed()->select('id', 'name');
            }])->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaRequest $request)
    {
        try {
            Area::create($request->validated());

            return response()->json([
                'message' => 'Área creada con éxito',
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'El área no se pudo crear',
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

            $area = Area::withTrashed()
                ->with(['institution' => function ($query) {
                    $query->withTrashed()->select('id', 'name');
                }])
                ->find($decryptedId);

            if (!$area) {
                return response()->json([
                    'message' => 'Área no encontrada',
                ], 404);
            }

            return new AreaResource($area);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Área no encontrada',
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAreaRequest $request, string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $area = Area::find($decryptedId);

            if (!$area) {
                return response()->json(['message' => 'Área no encontrada'], 404);
            }

            $validated = $request->validated();

            if (empty($validated)) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar.',
                ], 400);
            }

            $area->update($validated);

            return response()->json([
                'message' => 'Área actualizada con éxito.',
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
            $area = Area::find($decryptedId);

            if (!$area) {
                return response()->json(['message' => 'Área no encontrada'], 404);
            }

            $area->delete();

            return response()->json([
                'message' => 'Área desactivada con éxito',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $area = Area::onlyTrashed()->find($decryptedId);

            if (!$area) {
                return response()->json(['message' => 'Área no encontrada'], 404);
            }

            $area->restore();

            return response()->json([
                'message' => 'Área activada con éxito',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    /**
     * Permanently delete the resource.
     */
    public function forceDelete(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $area = Area::onlyTrashed()->find($decryptedId);

            if (!$area) {
                return response()->json(['message' => 'Área no encontrada'], 404);
            }

            $area->forceDelete();

            return response()->json([
                'message' => 'Área eliminada definitivamente con éxito',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    /**
     * List content depending on soft delete state.
     * - active  => solo activos
     * - inactive => solo eliminados (soft delete)
     * - all => todos
     */
    public function content($content)
    {
        try {
            switch ($content) {

                case 'active':
                    return AreaListResource::collection(
                        Area::with(['institution:id,name'])->paginate(10)
                    );

                case 'inactive':
                    return AreaListResource::collection(
                        Area::onlyTrashed()
                            ->with(['institution:id,name'])
                            ->paginate(10)
                    );

                case 'all':
                    return AreaListResource::collection(
                        Area::withTrashed()
                            ->with(['institution:id,name'])
                            ->paginate(10)
                    );

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

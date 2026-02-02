<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndexForCenso;
use App\Http\Resources\IndexForCenso\IndexForCensoResource;
use App\Http\Resources\IndexForCenso\IndexForCensoListResource;
use App\Http\Requests\IndexForCensoRequest\StoreIndexForCensoRequest;
use App\Http\Requests\IndexForCensoRequest\UpdateIndexForCensoRequest;
use Illuminate\Support\Facades\Crypt;

class IndexForCensoController extends Controller
{
    /**
     * GET /api/indexs-for-censos
     * Query params opcionales:
     *  - censos_id         (cifrado o numérico)
     *  - indexs_id         (cifrado o numérico)
     *  - reference_type    (section/sections/unit/units o FQCN)
     *  - reference_id      (cifrado o numérico)
     *  - include=reference (eager load polimórfico)
     */
    public function index(Request $request)
    {
        $query = IndexForCenso::with(['censo', 'index']);

        // Filtros
        if ($request->filled('censos_id')) {
            $query->where('censos_id', $this->decryptIfNeeded($request->input('censos_id')));
        }
        if ($request->filled('indexs_id')) {
            $query->where('indexs_id', $this->decryptIfNeeded($request->input('indexs_id')));
        }
        if ($request->filled('reference_type')) {
            $query->where('reference_type', $this->normalizeType($request->input('reference_type')));
        }
        if ($request->filled('reference_id')) {
            $query->where('reference_id', $this->decryptIfNeeded($request->input('reference_id')));
        }

        // Include polimórfico
        if (str_contains((string) $request->input('include'), 'reference')) {
            $query->with('reference');
        }

        $paginator = $query->paginate(10);

        return IndexForCensoListResource::collection($paginator);
    }

    /**
     * POST /api/indexs-for-censos
     * Validado con StoreIndexForCensoRequest
     */
    public function store(StoreIndexForCensoRequest $request)
    {
        try {
            IndexForCenso::create($request->validated());

            return response()->json([
                'message' => 'Índice asignado al censo con éxito.',
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'No se pudo crear el registro.',
            ], 400);
        }
    }

    /**
     * GET /api/indexs-for-censos/{id}
     */
    public function show(string $id, Request $request)
    {
        try {
            $decryptedId = Crypt::decryptString($id);

            $query = IndexForCenso::query();
            if (str_contains((string) $request->input('include'), 'reference')) {
                $query->with('reference');
            }

            $row = $query->find($decryptedId);

            if (!$row) {
                return response()->json(['message' => 'Registro no encontrado.'], 404);
            }

            return new IndexForCensoResource($row);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Registro no encontrado.'], 400);
        }
    }

    /**
     * PUT/PATCH /api/indexs-for-censos/{id}
     * Validado con UpdateIndexForCensoRequest
     */
    public function update(UpdateIndexForCensoRequest $request, string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $row = IndexForCenso::find($decryptedId);
            abort_if(!$row, 404, 'Registro no encontrado.');

            $validated = $request->validated();
            if (empty($validated)) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar.',
                ], 400);
            }

            $row->update($validated);

            return response()->json([
                'message' => 'Registro actualizado con éxito.',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al procesar la solicitud.'], 400);
        }
    }

    /**
     * DELETE /api/indexs-for-censos/{id} (soft delete)
     */
    public function destroy(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $row = IndexForCenso::find($decryptedId);

            if (!$row) {
                return response()->json(['message' => 'Registro no encontrado.'], 404);
            }

            $row->delete();

            return response()->json(['message' => 'Registro desactivado con éxito.'], 200);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al procesar la solicitud.'], 400);
        }
    }

    /**
     * POST /api/indexs-for-censos/{id}/restore
     */
    public function restore(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $row = IndexForCenso::onlyTrashed()->find($decryptedId);

            if (!$row) {
                return response()->json(['message' => 'Registro no encontrado.'], 404);
            }

            $row->restore();

            return response()->json(['message' => 'Registro restaurado con éxito.'], 200);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al procesar la solicitud.'], 400);
        }
    }

    /**
     * DELETE /api/indexs-for-censos/{id}/force-delete
     */
    public function forceDelete(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $row = IndexForCenso::onlyTrashed()->find($decryptedId);

            if (!$row) {
                return response()->json(['message' => 'Registro no encontrado.'], 404);
            }

            $row->forceDelete();

            return response()->json(['message' => 'Registro eliminado definitivamente.'], 200);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al procesar la solicitud.'], 400);
        }
    }

    /**
     * GET /api/indexs-for-censos/{content}/content
     * - active   => solo activos
     * - inactive => solo soft-deleted
     * - all      => todos
     * Soporta include=reference
     */
    public function content($content, Request $request)
    {
        try {
            $includeRef = str_contains((string) $request->input('include'), 'reference');

            if ($content === 'active') {
                $q = IndexForCenso::with(['censo', 'index']);
                if ($includeRef) $q->with('reference');
                return IndexForCensoListResource::collection($q->paginate(10));
            }

            if ($content === 'inactive') {
                $q = IndexForCenso::onlyTrashed()->with(['censo', 'index']);
                if ($includeRef) $q->with('reference');
                return IndexForCensoListResource::collection($q->paginate(10));
            }

            if ($content === 'all') {
                $q = IndexForCenso::withTrashed()->with(['censo', 'index']);
                if ($includeRef) $q->with('reference');
                return IndexForCensoListResource::collection($q->paginate(10));
            }

            return response()->json(['message' => 'Parámetro inválido.'], 400);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al procesar la solicitud.'], 400);
        }
    }

    /* -------------------- Helpers -------------------- */

    private function decryptIfNeeded($value)
    {
        if (is_null($value) || $value === '') return $value;
        if (is_numeric($value)) return (int) $value;
        try {
            return (int) Crypt::decryptString($value);
        } catch (\Throwable $e) {
            // Si no es numérico ni cifrado válido, forzará a no coincidir y no romperá la query
            return -1;
        }
    }

    private function normalizeType($type): string
    {
        $map = [
            'section'  => \App\Models\Section::class,
            'sections' => \App\Models\Section::class,
            'unit'     => \App\Models\Unit::class,
            'units'    => \App\Models\Unit::class,
        ];
        return $map[strtolower((string) $type)] ?? (string) $type;
    }
}

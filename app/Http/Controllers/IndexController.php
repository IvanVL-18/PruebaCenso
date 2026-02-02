<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Index;
use App\Http\Resources\Index\IndexResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\SelectorResource;
use App\Http\Requests\IndexRequest\StoreIndexRequest;
use App\Http\Requests\IndexRequest\UpdateIndexRequest;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndexsImport;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     * Selector para combos / selects.
     */
    public function index()
    {
        // Igual que en CensoController: devolvemos todos para selector
        return SelectorResource::collection(Index::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIndexRequest $request)
    {
        try {
            Index::create($request->validated());

            return response()->json([
                'message' => 'Índice creado con éxito',
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'El índice no se pudo crear',
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
            $index = Index::withTrashed()->find($decryptedId);

             if (!$index) {
                return response()->json([
                    'message' => 'Índice no encontrado',
                ], 404);
            }

            return new IndexResource($index);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Índice no encontrado',
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIndexRequest $request, string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $index = Index::find($decryptedId);

             if (!$index) {
                return response()->json([
                    'message' => 'Índice no encontrado',
                ], 404);
            }

            $validated = $request->validated();

            if (empty($validated)) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar.',
                ], 400);
            }

            $index->update($validated);

            return response()->json([
                'message' => 'Índice actualizado con éxito.',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $index = Index::find($decryptedId);

             if (!$index) {
                return response()->json([
                    'message' => 'Índice no encontrado',
                ], 404);
            }

            $index->delete();

            return response()->json([
                'message' => 'Índice desactivado con éxito',
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
            $index = Index::onlyTrashed()->find($decryptedId);

             if (!$index) {
                return response()->json([
                    'message' => 'Índice no encontrado',
                ], 404);
            }

            $index->restore();

            return response()->json([
                'message' => 'Índice activado con éxito',
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
            $index = Index::onlyTrashed()->find($decryptedId);

            if (!$index) {
                return response()->json([
                    'message' => 'Índice no encontrado',
                ], 404);
            }

            $index->forceDelete();

            return response()->json([
                'message' => 'Índice eliminado definitivamente con éxito',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    /**
     * List content depending on soft delete state.
     * - active    => solo activos
     * - inactive  => solo eliminados (soft)
     * - all       => todos
     */
    public function content($content)
    {
        try {
            switch ($content) {
                case 'active':
                    return ListResource::collection(Index::paginate(10));
                case 'inactive':
                    return ListResource::collection(Index::onlyTrashed()->paginate(10));
                case 'all':
                    return ListResource::collection(Index::withTrashed()->paginate(10));
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

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv|max:10240'
            ]);

            $file = $request->file('file');

            Excel::import(new IndexsImport, $file);

            return response()->json([
                'message' => 'Índices importados con éxito',
            ], 200);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Capturamos errores de validación específicos del Excel
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Fila ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return response()->json(['message' => 'Errores en el archivo', 'errors' => $errors], 422);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al procesar la solicitud', 'error' => $e->getMessage()], 400);
        }
    }
}
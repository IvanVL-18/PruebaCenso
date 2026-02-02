<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Option;
use App\Models\IndexForCensoQuestion;
use App\Http\Resources\Question\QuestionResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\SelectorResource;
use App\Http\Requests\QuestionRequest\StoreQuestionRequest;
use App\Http\Requests\QuestionRequest\UpdateQuestionRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()/* listado para un selector */
    {
        return SelectorResource::collection(Question::all());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuestionRequest $request)
    {
        $validated = $request->validated();
        try {
            DB::transaction(function () use ($validated) {
                $data = [
                    'name'     => $validated['name'],
                    'instructions' => $validated['instructions'] ?? null,
                    'type'         => $validated['type'],
                ];

                if (isset($validated['commentaries'])) {
                    $data['commentaries'] = $validated['commentaries'];
                }

                $question = Question::create($data);

                if ($validated['type'] !== 'empty' && ($validated['type'] == 'radio' || $validated['type'] == 'check' || $validated['type'] == 'selector')) {
                    foreach ($validated['options'] as $opt) {
                        Option::create([
                            'question_id' => $question->id,
                            'name'        => $opt['name'],
                        ]);
                    }
                }
            });

            return response()->json([
                'message' => 'Pregunta creada correctamente'
            ], 201);

        } catch (\Throwable $e) {

            return response()->json([
                'message' => 'Error al guardar los datos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)//muestra solo recursos activos
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $question = Question::find($decryptedId)->load('options');

            if (!$question) {
                return response()->json([
                    'message' => 'Pregunta no encontrada',
                ], 404);
            }

            return new QuestionResource($question);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Pregunta no encontrada'. $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestionRequest $request, string $id)//actualiza con las mismas reglas que la creacion y solo recursos activos
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $question = Question::find($decryptedId);

           if (!$question) {
                return response()->json([
                    'message' => 'Pregunta no encontrada',
                ], 404);
            }

            if (empty($request->validated())) {
                return response()->json([
                    'message' => 'No se enviaron datos para actualizar.',
                ], 400);
            }

            $question->update($request->validated());

            return response()->json([
                'message' => 'Pregunta actualizada con exito',
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
    {/* desactivamos la pregunta y a su vez las options */
        /* aqui tenemos que revisar que no es este ocupando en algun censo si se ocupa se le tiene que notificar */

         try {
            $decryptedId = Crypt::decryptString($id);      
            $question = Question::find($decryptedId);

           if (!$question) {
                return response()->json([
                    'message' => 'Pregunta no encontrada',
                ], 404);
            }

            /* buscar en la tabla indexforcensoquestion */
            $resutl = IndexForCensoQuestion::where('question_id', $decryptedId)->first();
            if ($resutl) {
                return response()->json([
                    'message' => 'La pregunta no se puede desactivar porque está asociada a un índice de censo.',
                ], 400);
            }

            /* desactivamor el recurso */
            $question->delete();
            //mensaje de exito 
            return response()->json([
                'message' => 'Pregunta desactivada con exito',
            ], 200);


        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    
    public function restore(string $id)//reactivar un recurso eliminado y solo busca en los eliminados
    {/* restauramos la pregunta y a su vez las options */
         try {
            $decryptedId = Crypt::decryptString($id);
             $question = Question::onlyTrashed()->find($decryptedId);
 
            if (!$question) {
                return response()->json([
                    'message' => 'Pregunta no encontrada',
                ], 404);
            }

            /* reactivamos el recurso */
            $question->restore();
            //mensaje de exito 
            return response()->json([
                'message' => 'Pregunta activada con exito',
            ], 200);


        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    public function forceDelete(string $id)//busca solo en los eliminados para eliminar definitivamente
    {/* eliminamos la pregunta y a su vez las options */
         try {
             $decryptedId = Crypt::decryptString($id);
            $question = Question::onlyTrashed()->find($decryptedId);

           if (!$question) {
                return response()->json([
                    'message' => 'Pregunta no encontrada',
                ], 404);
            }

            /* eliminamos definitivamente el recurso */
            $question->forceDelete();
            //mensaje de exito 
            return response()->json([
                'message' => 'Pregunta eliminada definitivamente con exito',
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
                    return QuestionResource::collection(Question::paginate(10));
                    break;
                case 'inactive':
                    return QuestionResource::collection(Question::onlyTrashed()->paginate(10));
                    break;
                case 'all':
                    return QuestionResource::collection(Question::withTrashed()->paginate(10));
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
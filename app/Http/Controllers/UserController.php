<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserIndexResource;
use App\Http\Resources\User\UserCollection;
use App\Http\Requests\UserRequest\StoreUserRequest;
use App\Http\Requests\UserRequest\StoreUserAdminRequest;
use App\Http\Requests\UserRequest\UpdateUserRequest;
use App\Http\Requests\UserRequest\ContentUserRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use App\Models\Occupation;
use App\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

use Illuminate\Contracts\Encryption\DecryptException;

class UserController extends Controller
{
        /**
     * Display a listing of the resource.
     */
    public function index()/* esta no se ocupa */
    {
        return UserIndexResource::collection(User::with(['role', 'occupation'])->paginate(10));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserAdminRequest $request)/* crea usuario con correo,contraseña y confirmación de contraseña */
    {
        try {
            User::create($request->validated());
            return response()->json([
                'message' => 'Usuario  creado con exito',
                /* aqui mandamos el correo al usuario con sus conraseñas */
            ], 201);
        } catch (\Throwable $e) {
           return response()->json([
                'message' => 'El usuario no se pudo crear',
            ], 400);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)//muestra solo recursos activos
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $user = User::find($decryptedId);

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            return new UserResource($user);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)//actualiza con las mismas reglas que la creacion y solo recursos activos
    {
        try {
            $decryptedId = Crypt::decryptString($id);
            $user = User::find($decryptedId);

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            $user->update($request->validated());
            return response()->json([
                'message' => 'Usuario actualizado con exito',
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
            $user = User::find($decryptedId);

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            /* desactivamor el recurso */
            $user->delete();
            //mensaje de exito 
            return response()->json([
                'message' => 'Usuario desactivado con exito',
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
            $user = User::onlyTrashed()->find($decryptedId);
 
            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            /* reactivamos el recurso */
            $user->restore();
            //mensaje de exito 
            return response()->json([
                'message' => 'Usuario activado con exito',
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
            $user = User::onlyTrashed()->find($decryptedId);

            if (!$user) {
                return response()->json([
                    'message' => 'Usuario no encontrado',
                ], 404);
            }

            /* eliminamos definitivamente el recurso */
            $user->forceDelete();
            //mensaje de exito 
            return response()->json([
                'message' => 'Usuario eliminado definitivamente con exito',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
            ], 400);
        }
    }

    /* falta modificar acorde a la vista */
    //metodo content para el soft delete
    public function content(ContentUserRequest $request)/* mostramos id,nombre completo y role */
    {
        return response()->json($request);
        try {
            /* valores que vienen del request */
            $content = $request->input('content', 'active');//el valor por default es active
            $roleId = $request->input('role_id'); // ya viene desencriptado y validado

            /* según el status mandado */
            $query = match ($content) {
                'active' => User::query(),
                'inactive' => User::onlyTrashed(),
                'all' => User::withTrashed(),
            };

            /* si existe el rol lo filtramos sino no */
            if (!empty($roleId)) {
                $query->where('role_id', $roleId);
            }

            /* Aqui agregamos paginación */
            $users = $query->paginate(10);

            return new UserCollection($users);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function import(Request $request)/* revisar el metodo */
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv|max:10240'
            ]);

            $file = $request->file('file');

            Excel::import(new UsersImport, $file); // Sin el \ antes de App

            return response()->json([
                'message' => 'Usuarios importados con éxito',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 400);
        }
    }

}

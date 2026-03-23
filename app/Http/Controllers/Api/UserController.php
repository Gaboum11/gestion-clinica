<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Obtener lista de usuarios.
     */
    public function index(): JsonResponse
    {
        $users = User::with('roles', 'specialty')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    /**
     * Crear nuevo usuario.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario creado exitosamente.',
                'data' => $user->load('roles', 'specialty'),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el usuario.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener usuario específico.
     */
    public function show(User $user): JsonResponse
    {
        try {
            $user->load('roles', 'specialty');

            return response()->json([
                'status' => 'success',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuario no encontrado.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Actualizar usuario.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $data = $request->validated();

            // Solo actualizar contraseña si se proporciona
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario actualizado exitosamente.',
                'data' => $user->load('roles', 'specialty'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el usuario.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar usuario.
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el usuario.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FullUpdateUserRequest;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $this->authorize('viewAny', \App\Models\User::class);
            $users = $this->userService->getAllUsers();
            return UserResource::collection($users);
        } catch (Throwable $e) {
            Log::error('User Index Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengambil daftar user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): UserResource|JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());
            return new UserResource($user);
        } catch (Throwable $e) {
            Log::error('User Store Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal membuat user baru.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): UserResource|JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan.'], 404);
            }

            $this->authorize('view', $user);
            return new UserResource($user);
        } catch (Throwable $e) {
            Log::error('User Show Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menampilkan detail user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Full update (PUT) - semua field wajib diisi.
     */
    public function fullUpdate(FullUpdateUserRequest $request, string $id): UserResource|JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan.'], 404);
            }

            $this->userService->updateUser($user, $request->validated());

            return new UserResource($user);
        } catch (Throwable $e) {
            Log::error('User Full Update Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui user (Full).',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Partial update (PATCH) - hanya field yang dikirim.
     */
    public function partialUpdate(UpdateUserRequest $request, string $id): UserResource|JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan.'], 404);
            }

            $this->userService->updateUser($user, $request->validated());

            return new UserResource($user);
        } catch (Throwable $e) {
            Log::error('User Partial Update Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui user (Partial).',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan.'], 404);
            }

            $this->authorize('delete', $user);

            // Jangan boleh delete diri sendiri
            if ($user->id === auth()->id()) {
                return response()->json([
                    'message' => 'Anda tidak diperbolehkan menghapus akun sendiri.'
                ], 403);
            }

            $this->userService->deleteUser($user);

            return response()->json([
                'message' => 'User deleted successfully.'
            ]);
        } catch (Throwable $e) {
            Log::error('User Delete Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menghapus user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

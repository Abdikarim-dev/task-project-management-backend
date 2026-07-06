<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->list(
            (int) $request->integer('per_page', 15),
            array_filter([
                'search' => $request->string('search')->toString() ?: null,
                'role' => $request->string('role')->toString() ?: null,
            ])
        );

        return ApiResponse::success([
            'items' => UserResource::collection($users->items())->resolve(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ], 'Users retrieved successfully.');
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        $user = $this->userService->find($user->id);

        if (! $user) {
            return ApiResponse::error('User not found.', 404);
        }

        return ApiResponse::success(
            new UserResource($user),
            'User retrieved successfully.'
        );
    }
}

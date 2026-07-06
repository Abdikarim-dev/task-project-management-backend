<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserDetailResource;
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

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return ApiResponse::success(
            new UserResource($user),
            'User created successfully.',
            201
        );
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        $detail = $this->userService->getDetail($user->id);

        if (! $detail) {
            return ApiResponse::error('User not found.', 404);
        }

        return ApiResponse::success(
            new UserDetailResource($detail['user'], [
                'projects' => $detail['projects'],
                'tasks_by_status' => $detail['tasks_by_status'],
                'projects_by_status' => $detail['projects_by_status'],
                'tasks_count' => $detail['tasks_count'],
            ]),
            'User retrieved successfully.'
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        return ApiResponse::success(
            new UserResource($user),
            'User updated successfully.'
        );
    }

    public function suspend(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        if ($user->isAdmin() && $user->id === auth()->id()) {
            return ApiResponse::error('You cannot suspend your own admin account.', 422);
        }

        $user = $this->userService->suspend($user, ! $user->is_suspended);

        return ApiResponse::success(
            new UserResource($user),
            $user->is_suspended ? 'User suspended successfully.' : 'User reactivated successfully.'
        );
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->update($request->user(), $request->validated());

        return ApiResponse::success(
            new UserResource($user),
            'Profile updated successfully.'
        );
    }
}

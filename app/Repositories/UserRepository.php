<?php

namespace App\Repositories;

use App\Models\User;
use App\Enums\UserRole;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->first();
    }

    public function getStaffMembers(): \Illuminate\Support\Collection
    {
        return User::query()
            ->where('role', UserRole::Staff)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = User::query()
            ->select(['id', 'name', 'email', 'role', 'created_at', 'updated_at']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        return $query->orderBy('name')->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function findWithRelations(int $id): ?User
    {
        return User::query()
            ->with(['projects:id,name,client_name,status,due_date', 'tasks:id,assigned_to,status,project_id'])
            ->find($id);
    }

    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }
}

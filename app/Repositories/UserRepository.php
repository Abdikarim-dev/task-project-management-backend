<?php

namespace App\Repositories;

use App\Models\User;
use App\Enums\UserRole;
use App\Repositories\Contracts\UserRepositoryInterface;

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
}

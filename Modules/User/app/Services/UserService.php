<?php

namespace Modules\User\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get paginated users filtered by search keyword and role.
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('roles', 'activeSubscription.plan');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Toggle user activation status (Active / Frozen).
     */
    public function toggleUserActive(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return $user;
    }

    /**
     * Block user and log the reason.
     */
    public function blockUser(int $userId, string $reason): User
    {
        $user = User::findOrFail($userId);
        $user->update([
            'is_blocked' => true,
            'block_reason' => $reason,
        ]);

        return $user;
    }

    /**
     * Unblock user and clear reason.
     */
    public function unblockUser(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->update([
            'is_blocked' => false,
            'block_reason' => null,
        ]);

        return $user;
    }
}

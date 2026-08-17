<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ApiAdminUserController extends Controller
{
    /**
     * Admin: List all platform users with roles and filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['roles', 'media', 'activeSubscription.plan']);

        if ($request->filled('role')) {
            $query->role($request->input('role'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Admin: Create new user with specific role.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,trainer,member',
            'password' => ['required', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
            'is_blocked' => false,
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $user->load('roles'),
        ], 201);
    }

    /**
     * Admin: Show single user details.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $user->load(['roles', 'media', 'subscriptions.plan', 'trainerBookings', 'wallet']),
        ]);
    }

    /**
     * Admin: Update user details and role.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|in:admin,trainer,member',
        ]);

        $user->update($validated);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $user->fresh('roles'),
        ]);
    }

    /**
     * Admin: Toggle user active status.
     */
    public function toggleActive(User $user): JsonResponse
    {
        $user->update(['is_active' => ! $user->is_active]);

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'User account activated.' : 'User account frozen/deactivated.',
            'data' => ['is_active' => $user->is_active],
        ]);
    }

    /**
     * Admin: Block abusive user.
     */
    public function block(Request $request, User $user): JsonResponse
    {
        $user->update([
            'is_blocked' => true,
            'blocked_reason' => $request->input('reason', 'Account blocked by administration for terms violations.'),
            'blocked_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User blocked successfully.',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Admin: Unblock user.
     */
    public function unblock(User $user): JsonResponse
    {
        $user->update([
            'is_blocked' => false,
            'blocked_reason' => null,
            'blocked_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User unblocked successfully.',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Admin: Delete user.
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return response()->json(['success' => false, 'message' => 'Cannot delete the sole administrator.'], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}

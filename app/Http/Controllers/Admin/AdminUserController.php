<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display list of all platform users with roles and status.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles', 'activeSubscription.plan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Toggle user activation status (Active / Frozen).
     */
    public function toggleActive(User $user): RedirectResponse
    {
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "User account has been {$status} successfully.");
    }

    /**
     * Instant Security Block User with Reason.
     */
    public function block(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'block_reason' => 'required|string|max:255',
        ]);

        $user->update([
            'is_blocked' => true,
            'block_reason' => $request->block_reason,
        ]);

        return redirect()->back()->with('success', 'User account has been suspended & blocked from accessing the system.');
    }

    /**
     * Unblock User.
     */
    public function unblock(User $user): RedirectResponse
    {
        $user->update([
            'is_blocked' => false,
            'block_reason' => null,
        ]);

        return redirect()->back()->with('success', 'User account has been unblocked successfully.');
    }
}
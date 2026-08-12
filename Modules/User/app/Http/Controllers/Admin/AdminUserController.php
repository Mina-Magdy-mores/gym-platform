<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\User\Http\Requests\Admin\BlockUserRequest;
use Modules\User\Services\UserService;

class AdminUserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display list of all platform users.
     */
    public function index(Request $request): View
    {
        $users = $this->userService->getPaginatedUsers($request->only(['search', 'role']));

        return view('user::admin.users.index', compact('users'));
    }

    /**
     * Toggle user activation status (Active / Frozen).
     */
    public function toggleActive(int $id): RedirectResponse
    {
        $user = $this->userService->toggleUserActive($id);
        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "User account has been {$status} successfully.");
    }

    /**
     * Instant Security Block User with Reason.
     */
    public function block(BlockUserRequest $request, int $id): RedirectResponse
    {
        $this->userService->blockUser($id, $request->validated('block_reason'));

        return redirect()->back()->with('success', 'User account has been suspended & blocked from accessing the system.');
    }

    /**
     * Unblock User.
     */
    public function unblock(int $id): RedirectResponse
    {
        $this->userService->unblockUser($id);

        return redirect()->back()->with('success', 'User account has been unblocked successfully.');
    }
}

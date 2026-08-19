<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class AdminActivityLogController extends Controller
{
    /**
     * Display comprehensive audit log dashboard with filters and change diffs.
     */
    public function index(Request $request): View
    {
        $query = Activity::with(['causer', 'subject'])->latest('id');

        // Filter by Log Domain (Model Name)
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->input('log_name'));
        }

        // Filter by Event Type (created, updated, deleted)
        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        // Filter by Causer (User ID)
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->input('causer_id'));
        }

        // Search by description or ID
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhere('subject_id', $search)
                  ->orWhereHasMorph('causer', [\App\Models\User::class], function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $logs = $query->paginate(25)->withQueryString();

        // Statistical Highlights
        $totalLogs = Activity::count();
        $todayLogs = Activity::whereDate('created_at', today())->count();
        $criticalLogs = Activity::whereIn('log_name', ['users', 'subscription_plans', 'payout_requests'])
            ->whereIn('event', ['updated', 'deleted'])
            ->count();
        $uniqueCausersCount = Activity::whereNotNull('causer_id')->distinct('causer_id')->count('causer_id');

        // Distinct log names for filter dropdown
        $availableLogNames = Activity::distinct()->pluck('log_name')->filter()->values();

        return view('user::admin.activity_logs.index', compact(
            'logs',
            'totalLogs',
            'todayLogs',
            'criticalLogs',
            'uniqueCausersCount',
            'availableLogNames'
        ));
    }
}

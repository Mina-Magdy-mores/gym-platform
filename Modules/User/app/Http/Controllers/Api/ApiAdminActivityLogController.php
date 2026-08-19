<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ApiAdminActivityLogController extends Controller
{
    /**
     * Admin: Retrieve paginated and filtered activity audit logs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Activity::with(['causer:id,name,email', 'subject'])->latest('id');

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->input('log_name'));
        }

        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhere('subject_id', $search);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $logs = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logs,
            'summary' => [
                'total_logs' => Activity::count(),
                'today_logs' => Activity::whereDate('created_at', today())->count(),
                'available_domains' => Activity::distinct()->pluck('log_name')->filter()->values(),
            ],
        ]);
    }
}

<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Payment\Models\Payment;

class AdminPaymentController extends Controller
{
    /**
     * Display Paymob-style master financial ledger dashboard with analytics & advanced filters.
     */
    public function index(Request $request): View
    {
        $query = Payment::with(['user', 'plan', 'booking.trainer'])->latest();

        // 1. Filter by Gateway
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->input('gateway'));
        }

        // 2. Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // 3. Filter by Payment Type (Subscription Plan vs PT Session)
        if ($request->filled('type')) {
            if ($request->input('type') === 'pt_session') {
                $query->whereNotNull('booking_id');
            } elseif ($request->input('type') === 'subscription') {
                $query->whereNotNull('subscription_plan_id');
            }
        }

        // 4. Filter by Search Keyword (Transaction ID, User Name, Email)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // 5. Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $payments = $query->paginate(25)->withQueryString();

        // Single Ultra-Fast SQL Aggregation Query for Master Ledger Stats
        $statsRaw = Payment::selectRaw("
            COUNT(*) as total_count,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) as total_gross,
            COALESCE(SUM(CASE WHEN status = 'completed' AND subscription_plan_id IS NOT NULL THEN amount ELSE 0 END), 0) as total_subscriptions,
            COALESCE(SUM(CASE WHEN status = 'completed' AND booking_id IS NOT NULL THEN amount ELSE 0 END), 0) as total_pt
        ")->first();

        $totalPtSessionsRevenue = (float) ($statsRaw->total_pt ?? 0);

        $stats = [
            'total_gross_revenue' => (float) ($statsRaw->total_gross ?? 0),
            'total_subscriptions_revenue' => (float) ($statsRaw->total_subscriptions ?? 0),
            'total_pt_sessions_revenue' => $totalPtSessionsRevenue,
            'total_gym_commission_15pct' => round($totalPtSessionsRevenue * 0.15, 2),
            'total_transactions_count' => (int) ($statsRaw->total_count ?? 0),
        ];

        return view('payment::admin_index', compact('payments', 'stats'));
    }
}

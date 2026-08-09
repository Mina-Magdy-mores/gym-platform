<?php

namespace Modules\Payment\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payment\Models\Payment;
use Modules\User\Traits\ApiResponseTrait;

class ApiAdminPaymentController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all financial ledger payments with filters & stats for mobile admin.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['user', 'plan', 'booking.trainer'])->latest();

        if ($request->filled('gateway')) {
            $query->where('gateway', $request->input('gateway'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            if ($request->input('type') === 'pt_session') {
                $query->whereNotNull('booking_id');
            } elseif ($request->input('type') === 'subscription') {
                $query->whereNotNull('subscription_plan_id');
            }
        }

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

        $payments = $query->paginate(20);

        // Single Ultra-Fast SQL Aggregation Query for Master Ledger Stats
        $statsRaw = Payment::selectRaw("
            COUNT(*) as total_count,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) as total_gross,
            COALESCE(SUM(CASE WHEN status = 'completed' AND subscription_plan_id IS NOT NULL THEN amount ELSE 0 END), 0) as total_subscriptions,
            COALESCE(SUM(CASE WHEN status = 'completed' AND booking_id IS NOT NULL THEN amount ELSE 0 END), 0) as total_pt
        ")->first();

        $totalPtSessionsRevenue = (float) ($statsRaw->total_pt ?? 0);

        $data = [
            'stats' => [
                'total_gross_revenue' => (float) ($statsRaw->total_gross ?? 0),
                'total_subscriptions_revenue' => (float) ($statsRaw->total_subscriptions ?? 0),
                'total_pt_sessions_revenue' => $totalPtSessionsRevenue,
                'net_gym_commission_15pct' => round($totalPtSessionsRevenue * 0.15, 2),
                'total_transactions_count' => (int) ($statsRaw->total_count ?? 0),
            ],
            'payments' => $payments->map(function ($p) {
                return [
                    'id' => $p->id,
                    'transaction_id' => $p->transaction_id,
                    'user' => [
                        'id' => $p->user_id,
                        'name' => $p->user->name ?? 'User',
                        'email' => $p->user->email ?? '',
                    ],
                    'type' => $p->booking_id ? 'pt_session' : 'subscription',
                    'gateway' => $p->gateway,
                    'amount' => (float) $p->amount,
                    'currency' => $p->currency ?? 'EGP',
                    'status' => $p->status,
                    'invoice_download_url' => route('api.v1.invoices.download', $p->id),
                    'created_at' => $p->created_at?->toIso8601String(),
                ];
            }),
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'total' => $payments->total(),
            ],
        ];

        return $this->successResponse($data, 'Admin financial ledger fetched successfully.');
    }
}

<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Services\BookingService;

class AdminBookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display all platform session bookings for Master Admin Control Panel with filtering.
     */
    public function index(Request $request): View
    {
        $query = Booking::with(['user.activeSubscription.plan', 'trainer', 'walletTransaction', 'payment']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('trainer', function ($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('trainer_id')) {
            $query->where('trainer_id', $request->input('trainer_id'));
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();
        $trainers = \App\Models\User::role('trainer')->get();

        $stats = [
            'total'     => Booking::count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('subscription::admin.bookings.index', compact('bookings', 'trainers', 'stats'));
    }

    /**
     * Resolve and issue refund for a booking session.
     */
    public function processRefund(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'refund_method' => 'required|string|in:auto_gateway,instapay,vodafone_cash,in_gym_cash',
            'notes' => 'nullable|string|max:255',
        ]);

        $result = $this->bookingService->processBookingRefund(
            $booking,
            $request->input('refund_method'),
            $request->input('notes')
        );

        if ($result['status']) {
            return redirect()->route('admin.bookings.index')->with('success', $result['message']);
        }

        return redirect()->route('admin.bookings.index')->with('error', $result['message']);
    }
}

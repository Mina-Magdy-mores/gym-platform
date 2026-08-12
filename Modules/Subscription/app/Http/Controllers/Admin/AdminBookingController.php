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
     * Display all platform session bookings for Master Admin Control Panel.
     */
    public function index(Request $request): View
    {
        $bookings = $this->bookingService->getAllBookingsForAdmin();

        return view('subscription::admin.bookings.index', compact('bookings'));
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

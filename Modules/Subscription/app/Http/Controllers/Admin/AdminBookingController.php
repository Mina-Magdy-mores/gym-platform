<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
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
}

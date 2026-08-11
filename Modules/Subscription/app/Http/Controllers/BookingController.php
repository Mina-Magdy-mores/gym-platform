<?php

namespace Modules\Subscription\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Payment\Services\PaymentService;
use Modules\Subscription\Http\Requests\BookSessionRequest;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Services\BookingService;

class BookingController extends Controller
{
    protected BookingService $bookingService;
    protected PaymentService $paymentService;

    public function __construct(BookingService $bookingService, PaymentService $paymentService)
    {
        $this->bookingService = $bookingService;
        $this->paymentService = $paymentService;
    }

    /**
     * Display trainer bookings page via service layer.
     */
    public function index(Request $request): View
    {
        $trainers = $this->bookingService->getAllTrainers();
        $bookings = $this->bookingService->getUserBookings($request->user());

        return view('subscription::bookings', compact('trainers', 'bookings'));
    }

    /**
     * Book a private trainer session via Web form with out-of-pocket payment fallback.
     */
    public function store(BookSessionRequest $request): RedirectResponse
    {
        try {
            $this->bookingService->bookTrainerSession(
                $request->user(),
                $request->validated()
            );

            return redirect()->route('bookings.index')->with('status', 'booked');
        } catch (\Exception $e) {
            if ($e->getMessage() === 'OUT_OF_POCKET_PAYMENT_REQUIRED') {
                $validated = $request->validated();
                $trainer = User::findOrFail($validated['trainer_id']);

                $paymentResponse = $this->paymentService->processPTSessionPayment(
                    $request->user(),
                    $trainer,
                    $validated,
                    $request->input('gateway', 'paymob')
                );

                if ($paymentResponse->redirectUrl) {
                    return redirect()->away($paymentResponse->redirectUrl);
                }

                return redirect()->route('bookings.index')->with('status', 'booked');
            }

            return redirect()->route('bookings.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel a booking session via Web interface.
     */
    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        if ($request->user()->id !== $booking->user_id && $request->user()->id !== $booking->trainer_id && !$request->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $result = $this->bookingService->cancelBooking($booking, $request->input('refund_method', 'instapay'));

        if ($result['status']) {
            return redirect()->route('bookings.index')->with('success', $result['message']);
        }

        return redirect()->route('bookings.index')->with('error', $result['message']);
    }
}
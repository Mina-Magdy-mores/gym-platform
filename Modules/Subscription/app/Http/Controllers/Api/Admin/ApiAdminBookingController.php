<?php

namespace Modules\Subscription\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Services\BookingService;
use Modules\Subscription\Transformers\BookingResource;
use Modules\User\Traits\ApiResponseTrait;

class ApiAdminBookingController extends Controller
{
    use ApiResponseTrait;

    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display all platform session bookings for Master Admin Control Panel via REST API.
     */
    public function index(): JsonResponse
    {
        $bookings = $this->bookingService->getAllBookingsForAdmin();

        return $this->successResponse(
            BookingResource::collection($bookings),
            'Master Admin bookings ledger retrieved successfully.'
        );
    }

    /**
     * Resolve and issue refund for a booking session via REST API.
     */
    public function processRefund(Request $request, Booking $booking): JsonResponse
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
            $booking->refresh();
            return $this->successResponse(
                new BookingResource($booking),
                $result['message']
            );
        }

        return $this->errorResponse($result['message'], 400);
    }
}

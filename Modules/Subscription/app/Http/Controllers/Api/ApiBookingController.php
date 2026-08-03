<?php

namespace Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Subscription\Http\Requests\BookSessionRequest;
use Modules\Subscription\Services\BookingService;
use Modules\Subscription\Transformers\BookingResource;
use Modules\User\Traits\ApiResponseTrait;

class ApiBookingController extends Controller
{
    use ApiResponseTrait;

    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Get bookings for the authenticated member or trainer.
     */
    public function index(Request $request): JsonResponse
    {
        $bookings = $this->bookingService->getUserBookings($request->user());

        $data = [
            'bookings' => BookingResource::collection($bookings),
        ];

        return $this->successResponse($data, 'Bookings fetched successfully.');
    }

    /**
     * Book a private trainer session with concurrency check.
     */
    public function store(BookSessionRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->bookTrainerSession(
                $request->user(),
                $request->validated()
            );

            $booking->load(['user', 'trainer']);

            $data = [
                'booking' => new BookingResource($booking),
            ];

            return $this->successResponse($data, 'Trainer session booked successfully.', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}
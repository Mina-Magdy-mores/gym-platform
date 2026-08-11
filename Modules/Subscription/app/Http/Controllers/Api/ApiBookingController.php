<?php

namespace Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payment\Services\PaymentService;
use Modules\Subscription\Http\Requests\BookSessionRequest;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Services\BookingService;
use Modules\Subscription\Transformers\BookingResource;
use Modules\User\Traits\ApiResponseTrait;

class ApiBookingController extends Controller
{
    use ApiResponseTrait;

    protected BookingService $bookingService;
    protected PaymentService $paymentService;

    public function __construct(BookingService $bookingService, PaymentService $paymentService)
    {
        $this->bookingService = $bookingService;
        $this->paymentService = $paymentService;
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
     * Book a private trainer session with concurrency check & out-of-pocket payment fallback.
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
                'requires_payment' => false,
                'booking' => new BookingResource($booking),
            ];

            return $this->successResponse($data, 'Trainer session booked successfully.', 201);
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

                $data = [
                    'requires_payment' => true,
                    'is_successful' => $paymentResponse->isSuccessful,
                    'transaction_id' => $paymentResponse->transactionId,
                    'redirect_url' => $paymentResponse->redirectUrl,
                    'message' => '0 PT sessions remaining in plan. Please complete checkout to confirm booking.',
                ];

                return $this->successResponse($data, 'PT Session payment checkout initiated.', 202);
            }

            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * Cancel a booking session via REST API.
     */
    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        if ($request->user()->id !== $booking->user_id && $request->user()->id !== $booking->trainer_id && !$request->user()->hasRole('Admin')) {
            return $this->errorResponse('Unauthorized action.', 403);
        }

        $result = $this->bookingService->cancelBooking($booking, $request->input('refund_method', 'instapay'));

        if ($result['status']) {
            $booking->refresh();
            return $this->successResponse([
                'booking' => new BookingResource($booking),
                'is_eligible' => $result['is_eligible'],
            ], $result['message']);
        }

        return $this->errorResponse($result['message'], 400);
    }
}
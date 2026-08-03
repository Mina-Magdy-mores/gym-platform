<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\User\Http\Requests\Api\ApiProfileUpdateRequest;
use Modules\User\Services\ProfileService;
use Modules\User\Traits\ApiResponseTrait;
use Modules\User\Transformers\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiProfileController extends Controller
{
    use ApiResponseTrait;

    protected ProfileService $profileService;

    /**
     * Inject ProfileService dependency.
     */
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Display current authenticated user profile.
     */
    public function show(Request $request): JsonResponse
    {
        $data = [
            'user' => new UserResource($request->user()),
        ];

        return $this->successResponse(
            $data,
            'Profile retrieved successfully.'
        );
    }

    /**
     * Update current authenticated user profile and media attachments.
     */
    public function update(ApiProfileUpdateRequest $request): JsonResponse
    {
        $user = $this->profileService->updateProfile(
            $request->user(),
            $request->validated()
        );

        $data = [
            'user' => new UserResource($user),
        ];

        return $this->successResponse(
            $data,
            'Profile updated successfully.'
        );
    }
    /**
     * Delete a specific media item belonging to the user.
     */
    public function destroyMedia(Request $request, int $mediaId): JsonResponse
    {
        $deleted = $this->profileService->deleteMedia($request->user(), $mediaId);

        if (! $deleted) {
            return $this->errorResponse('Media file not found or unauthorized access.', 404);
        }

        $data = [
            'user' => new UserResource($request->user()->fresh()),
        ];

        return $this->successResponse(
            $data,
            'Media file deleted successfully.'
        );
    }
}
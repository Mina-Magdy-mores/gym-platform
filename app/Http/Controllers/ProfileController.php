<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Modules\User\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    /**
     * Inject ProfileService dependency.
     */
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information and media attachments.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $this->profileService->updateProfile($user, $request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    /**
     * Delete a specific media item belonging to the user.
     */
    public function destroyMedia(Request $request, int $mediaId): RedirectResponse
    {
        $deleted = $this->profileService->deleteMedia($request->user(), $mediaId);

        if (! $deleted) {
            return Redirect::route('profile.edit')->with('error', 'Media file not found.');
        }

        return Redirect::route('profile.edit')->with('status', 'media-deleted');
    }
}
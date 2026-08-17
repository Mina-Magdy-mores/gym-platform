<?php

namespace Modules\User\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    /**
     * Update user profile data and media attachments.
     */
    public function updateProfile(User $user, array $data): User
    {
        // Update text fields if present
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['email'])) {
            if ($user->email !== $data['email']) {
                $user->email = $data['email'];
                $user->email_verified_at = null;
            }
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Handle Avatar Upload (Replaces old avatar automatically due to singleFile())
        if (isset($data['avatar'])) {
            $user->addMedia($data['avatar'])
                ->toMediaCollection('avatar');
        }

        // Handle Multiple Trainer Certificates Upload
        if (isset($data['certificates']) && is_array($data['certificates'])) {
            foreach ($data['certificates'] as $certificate) {
                $user->addMedia($certificate)
                    ->toMediaCollection('certificates');
            }
        }

        return $user->fresh();
    }
    /**
     * Delete a specific media item belonging to the user.
     */
    public function deleteMedia(User $user, int $mediaId): bool
    {
        $mediaItem = $user->media()->find($mediaId);

        if (! $mediaItem) {
            return false;
        }

        $mediaItem->delete();

        return true;
    }
}
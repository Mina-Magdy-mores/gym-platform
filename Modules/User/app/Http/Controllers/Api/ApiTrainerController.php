<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ApiTrainerController extends Controller
{
    /**
     * Public list of certified trainers with profiles, avatars, specialties & session rates.
     */
    public function publicIndex(): JsonResponse
    {
        $trainers = User::role('trainer')
            ->where('is_active', true)
            ->where('is_blocked', false)
            ->with(['media'])
            ->get()
            ->map(function ($trainer) {
                return [
                    'id' => $trainer->id,
                    'name' => $trainer->name,
                    'email' => $trainer->email,
                    'phone' => $trainer->phone,
                    'avatar' => $trainer->getFirstMediaUrl('avatar') ?: 'https://ui-avatars.com/api/?name=' . urlencode($trainer->name) . '&background=ff5b00&color=fff',
                    'session_rate' => (float) ($trainer->session_rate ?? 200.00),
                    'specialties' => $trainer->specialties ?? 'Bodybuilding & Fitness Coaching',
                    'bio' => $trainer->bio ?? 'Certified fitness coach dedicated to transforming your physique.',
                    'certificates' => $trainer->getMedia('certificates')->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'name' => $media->name,
                            'url' => $media->getUrl(),
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $trainers,
        ]);
    }

    /**
     * Admin: List all trainers with comprehensive statistics.
     */
    public function index(): JsonResponse
    {
        $trainers = User::role('trainer')
            ->with(['media', 'wallet', 'trainerBookings'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $trainers,
        ]);
    }

    /**
     * Admin: Recruit new certified trainer.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'session_rate' => 'required|numeric|min:50|max:5000',
            'specialties' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'password' => ['required', Password::defaults()],
        ]);

        $trainer = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'session_rate' => $validated['session_rate'],
            'specialties' => $validated['specialties'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
            'is_blocked' => false,
        ]);

        $trainer->assignRole('trainer');

        return response()->json([
            'success' => true,
            'message' => 'Trainer recruited successfully.',
            'data' => $trainer->load('roles'),
        ], 201);
    }

    /**
     * Admin: Promote existing user to trainer.
     */
    public function promote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'session_rate' => 'required|numeric|min:50|max:5000',
            'specialties' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->syncRoles(['trainer']);
        $user->update([
            'session_rate' => $validated['session_rate'],
            'specialties' => $validated['specialties'] ?? $user->specialties,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member promoted to certified trainer successfully.',
            'data' => $user->fresh('roles'),
        ]);
    }

    /**
     * Admin: Upload certifications for trainer.
     */
    public function uploadCertificates(Request $request, User $trainer): JsonResponse
    {
        $request->validate([
            'certificates.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('certificates')) {
            foreach ($request->file('certificates') as $file) {
                $trainer->addMedia($file)->toMediaCollection('certificates');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Certificates uploaded successfully.',
            'data' => $trainer->getMedia('certificates'),
        ]);
    }

    /**
     * Admin: Delete trainer certificate.
     */
    public function deleteCertificate(int $mediaId): JsonResponse
    {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Certificate deleted successfully.',
        ]);
    }

    /**
     * Admin: Remove trainer role.
     */
    public function destroy(User $trainer): JsonResponse
    {
        $trainer->syncRoles(['member']);

        return response()->json([
            'success' => true,
            'message' => 'Trainer demoted to regular member.',
        ]);
    }
}

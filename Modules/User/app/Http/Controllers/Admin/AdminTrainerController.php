<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Modules\Wallet\Models\TrainerWallet;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Models\Role;

class AdminTrainerController extends Controller
{
    /**
     * Display all certified trainers, application dossiers, and certification credentials.
     */
    public function index(Request $request): View
    {
        $trainers = User::role('trainer')
            ->with(['media', 'trainerWallet', 'trainerBookings'])
            ->withCount(['trainerBookings as completed_sessions_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->latest()
            ->paginate(15);

        // Fetch registered users (members) who are not yet trainers so Admin can promote them
        $availableMembers = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'trainer');
        })->get();

        return view('user::admin.trainers.index', compact('trainers', 'availableMembers'));
    }

    /**
     * Store and recruit a new certified personal trainer.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'nullable|string|max:20',
            'password'       => 'required|string|min:8',
            'avatar'         => 'nullable|image|max:4096',
            'certificates'   => 'nullable|array',
            'certificates.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?? null,
            'password'          => Hash::make($validated['password']),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);

        // Assign Trainer Role
        $trainerRole = Role::firstOrCreate(['name' => 'trainer']);
        $user->assignRole($trainerRole);

        // Initialize Trainer Wallet
        TrainerWallet::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'balance'      => 0.00,
            'total_earned' => 0.00,
        ]);

        // Upload Profile Avatar if provided
        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')
                ->toMediaCollection('avatar');
        }

        // Upload Certification documents if provided
        if ($request->hasFile('certificates')) {
            foreach ($request->file('certificates') as $file) {
                $user->addMedia($file)
                    ->toMediaCollection('certificates');
            }
        }

        return redirect()->route('admin.trainers.index')->with('success', "Coach {$user->name} has been successfully hired and added to the roster!");
    }

    /**
     * Promote an existing registered platform member to a Coach/Trainer.
     */
    public function promote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'certificates'   => 'nullable|array',
            'certificates.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $user = User::findOrFail($validated['user_id']);

        $trainerRole = Role::firstOrCreate(['name' => 'trainer']);
        if (! $user->hasRole('trainer')) {
            $user->assignRole($trainerRole);
        }

        // Initialize Trainer Wallet
        TrainerWallet::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'balance'      => 0.00,
            'total_earned' => 0.00,
        ]);

        // Upload Certification documents if provided
        if ($request->hasFile('certificates')) {
            foreach ($request->file('certificates') as $file) {
                $user->addMedia($file)->toMediaCollection('certificates');
            }
        }

        return redirect()->route('admin.trainers.index')->with('success', "{$user->name} has been successfully promoted to Certified Coach!");
    }

    /**
     * Upload additional certification credentials to an existing trainer's dossier.
     */
    public function uploadCertificates(Request $request, User $trainer): RedirectResponse
    {
        $request->validate([
            'certificates'   => 'required|array|min:1',
            'certificates.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        foreach ($request->file('certificates') as $file) {
            $trainer->addMedia($file)->toMediaCollection('certificates');
        }

        return redirect()->back()->with('success', 'Certification documents uploaded successfully.');
    }

    /**
     * Delete a single certificate media item.
     */
    public function deleteCertificate(Media $media): RedirectResponse
    {
        $media->delete();

        return redirect()->back()->with('success', 'Certificate document removed.');
    }

    /**
     * Delete / Dismiss a trainer from the coaching roster.
     */
    public function destroy(User $trainer): RedirectResponse
    {
        $name = $trainer->name;
        
        // Remove trainer role
        $trainer->removeRole('trainer');

        return redirect()->route('admin.trainers.index')->with('success', "Coach {$name} has been removed from the coaching roster.");
    }
}

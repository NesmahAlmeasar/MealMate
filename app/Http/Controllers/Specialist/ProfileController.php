<?php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\Diet;
use App\Models\Nutritionist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the specialist's profile
     */
    /**
     * Display the user's profile
     */
    public function show()
    {
        $user = Auth::user();

        // Get nutritionist record only if user is Specialist or Nutrition Manager
        $nutritionist = null;
        if ($user->hasRole('Specialist') || $user->hasRole('Nutrition Manager')) {
            $nutritionist = Nutritionist::firstOrCreate(
                ['nutritionist_id' => $user->user_id],
                [
                    'Academic_level' => 'Specialist',
                    'description' => '',
                ]
            );
        }

        // Get statistics
        $dietsCount = Diet::where('nutritionist_id', $user->user_id)->count();

        return view('specialist.profile', compact('user', 'nutritionist', 'dietsCount'));
    }

    /**
     * Update the specialist's profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'Fname' => 'required|string|max:255',
            'Lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->user_id.',user_id',
            'phone' => 'nullable|string|max:20',
            'Academic_level' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Update user information
        $user->Fname = $validated['Fname'];
        $user->Lname = $validated['Lname'];
        $user->email = $validated['email'];

        if (! empty($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo_url) {
                Storage::disk('public')->delete($user->photo_url);
            }
            $user->photo_url = $request->file('photo')->store('profiles', 'public');
        }

        // Update password if provided
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $user->save();

        // Update or create nutritionist record ONLY for relevant roles
        if ($user->hasRole('Specialist') || $user->hasRole('Nutrition Manager')) {
            $nutritionist = Nutritionist::updateOrCreate(
                ['nutritionist_id' => $user->user_id],
                [
                    'Academic_level' => $validated['Academic_level'] ?? 'Specialist',
                    'description' => $validated['description'] ?? '',
                ]
            );
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }
}

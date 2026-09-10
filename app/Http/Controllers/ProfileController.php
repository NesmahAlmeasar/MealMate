<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
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
     * عرض صفحة الملف الشخصي
     */
    public function show()
    {
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            // $request->user()->email_verified_at = null; // Removed
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * تحديث الصورة الشخصية
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // حذف الصورة القديمة إذا كانت موجودة
        if ($user->photo_url && \Storage::disk('public')->exists($user->photo_url)) {
            \Storage::disk('public')->delete($user->photo_url);
        }

        // رفع الصورة الجديدة
        $path = $request->file('photo')->store('profile_photos', 'public');

        $user->update(['photo_url' => $path]);

        return Redirect::route('profile.show')
            ->with('success', 'تم تحديث الصورة الشخصية بنجاح');
    }

    /**
     * تحديث كلمة المرور
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => \Hash::make($request->password),
        ]);

        return Redirect::route('profile.show')
            ->with('success', 'تم تحديث كلمة المرور بنجاح');
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
}

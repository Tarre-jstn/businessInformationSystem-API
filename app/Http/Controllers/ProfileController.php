<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    // public function update(ProfileUpdateRequest $request): RedirectResponse
    // {
    //     $request->user()->fill($request->validated());

    //     if ($request->user()->isDirty('email')) {
    //         $request->user()->email_verified_at = null;
    //     }

    //     $request->user()->save();

    //     return Redirect::route('profile.edit');
    // }

    public function update(int $id, Request $request)
    {
        $request->validate([
            'profile_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'email'=> 'required|string|lowercase|email|max:255', 
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255'
        ]);

        $user = User::find(Auth::id());

       
        if ($request->hasFile('profile_img')) {
            // Handle the file upload
            $image = $request->file('profile_img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            
            // Save the image to storage/app/public/business_logos
            $image->storeAs('public/user_profile', $imageName);
    
            // Save the image name in the database (just the filename, not the full path)
            $user->profile_img = $imageName;
        }
    

        // Update other business fields
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->address = $request->input('address');
        $user->contact_number = $request->input('contact_number');

        // Save the business profile
        $user->save();

        return response()->json(['success' => true]);
       
            Log::error('Image Upload Error: ' . $e->getMessage());

            return back()->withErrors(['profile_img' => 'An error occurred while uploading the image.']);
        
    }

    public function show(Request $request){
        return response()->json(Auth::user());
    }
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function show()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user->name = $request->name;

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image && File::exists(public_path($user->profile_image))) {
                File::delete(public_path($user->profile_image));
            }

            $imageName = time() . '.' . $request->profile_image->extension();
            $request->profile_image->move(public_path('profiles'), $imageName);
            $user->profile_image = 'profiles/' . $imageName;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}

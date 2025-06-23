<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User; // Pastikan model User di-import

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profil');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'current_password' => 'required_with:password|current_password',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Menggunakan model User untuk update
        $user = User::findOrFail($user->id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password')
                          ? Hash::make($request->password)
                          : $user->password
        ]);

        return redirect()->route('profil')->with('success', 'Profil berhasil diperbarui!');
    }
}

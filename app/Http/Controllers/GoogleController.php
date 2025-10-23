<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller; 
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(Str::random(16)), // Safer than hardcoded password
                ]);
            }

            Auth::login($user);

            $user->createToken('auth_token')->plainTextToken;

            return redirect()->route('dashboard')->with('toast_success', 'Login Successful!');
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Unauthorized']);
        }
    }
}

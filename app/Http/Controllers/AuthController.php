<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chair;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->level = 'User';

        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return redirect('/')->with('toast_success', 'Registration successful!')
            ->with('access_token', $token);
    }

    public function signin(Request $request)
    {
        if ($request->has('qrToken')) {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'storeId' => 'required|string|max:255',
                'qrToken' => 'required|string|exists:chairs,qr_token',
            ]);

            $name = $validatedData['name'];
            $storeId = $validatedData['storeId'];
            $qrToken = $validatedData['qrToken'];

            $chair = Chair::where('qr_token', $qrToken)->first();

            if (!$chair) {
                return redirect()->route('login')->withErrors(['error' => 'Invalid QR Token']);
            }

            $deviceId = Str::random(16);
            $qrTokenNew = Str::random(32);
            $chair = Chair::create([
                'name' => $name,
                'store_id' => $storeId,
                'level' => 'Pivot',
                'email' => $deviceId . '@device.com',
                'password' => bcrypt('123456'),
                'qr_token' => $qrTokenNew, // Ensure qrToken is set
            ]);

            Auth::guard('chair')->login($chair);

            $token = $chair->createToken('auth_token')->plainTextToken;

            return redirect()->route('user-home')
                ->with('auth_token', $token)
                ->with('toast_success', 'Login successful!');
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return redirect()->route('login')->withErrors(['email' => 'Unauthorized']);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return redirect()->route('dashboard')->with('auth_token', $token)->with('toast_success', 'Login successful!');
    }
    
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete(); // Delete all tokens
        }

        $request->session()->invalidate();
        
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('toast_success', 'Logged Out Successful!');
    }
}

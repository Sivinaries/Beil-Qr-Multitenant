<?php

namespace App\Http\Controllers;

use App\Models\Chair;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChairController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $userStore = Auth::user()->store;

        if (!$userStore) {
            return redirect()->route('addstore');
        }

        $cacheKey = 'chairs';

        $users = Cache::remember($cacheKey, 60, function () use ($userStore) {
            return $userStore->chairs()->where('level', 'Chair')->get();
        });

        return view('chair', compact('users'));
    }

    public function create()
    {

        return view('addchair');
    }

    public function store(Request $request)
    {
        $userStore = auth()->user()->store->id;

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $qrToken = Str::random(32);

        $deviceId = Str::random(16); // Always generates a new 16 character string

        $data = array_merge(
            $data,
            [
                'email' => $deviceId . '@device.com', // You can customize the email or use the chair's device_id
                'password' => bcrypt('123456'), // Set a default password or handle the password field as needed
                'level' => 'Chair',
                'qr_token' => $qrToken,
                'store_id' => $userStore,
            ]
        );

        Chair::create($data);

        Cache::forget('chairs');

        return redirect('/chair')->with('toast_success', 'Registration successful!');
    }

    public function destroy($id)
    {
        Chair::destroy($id);

        Cache::forget('chairs');

        return redirect(route('chair'))->with('success', 'Kursi Berhasil Dihapus !');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class WithdrawController extends Controller
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

        $status = $userStore->status;

        if ($status !== 'Settlement') {
            return redirect()->route('login');
        }

        $cacheKey = 'withdraws_user_' . Auth::id();

        $withdraws = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userStore) {
            return $userStore->withdraws;
        });

        return view('withdraw', compact('withdraws'));
    }

    public function create()
    {
        return view('addwithdraw');
    }

    public function store(Request $request)
    {
        $userStore = auth()->user()->store;

        // Validasi input dari form
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'payment_type' => 'required|in:BCA,BNI,OVO,GOPAY,DANA',
            'amount'       => 'required|numeric|min:1',
            'no_rek'       => 'required|string|max:50',
            'note'         => 'nullable|string|max:200',
        ]);

        // Tambahkan data tambahan
        $data['store_id'] = $userStore->id;
        $data['status']   = 'pending'; // default status, biar tidak bisa diset manual dari form

        // Simpan ke database
        Withdraw::create($data);

        // Hapus cache lama jika ada
        Cache::forget('withdraws_user_' . Auth::id());

        return redirect()->route('withdraw')->with('success', 'Withdraw successfully created!');
    }

    public function show($id)
    {
        $withdraw = Cache::remember("withdraw_{$id}", now()->addMinutes(60), function () use ($id) {
            return Withdraw::findOrFail($id);
        });

        return view('showwithdraw', compact('withdraw'));
    }
}

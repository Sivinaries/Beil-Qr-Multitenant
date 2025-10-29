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
        //
    }

    public function show(Withdraw $withdraw)
    {
        //
    }
}

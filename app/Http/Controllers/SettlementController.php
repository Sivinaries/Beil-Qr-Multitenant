<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SettlementController extends Controller
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

        $cacheKey = 'settlements_user_' . Auth::id();

        $settlements = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userStore) {
            return $userStore->settlements()->with('user')->get();
        });

        return view('settlement', compact('settlements'));
    }

    public function startamount()
    {
        return view('addstartamount');
    }

    public function poststart(Request $request)
    {
        $store = auth()->user()->store;

        $user = auth()->user();

        $data = $request->validate([
            'start_amount' => 'nullable|numeric',
        ]);

        $data['user_id'] = $user->id;
        $data['store_id'] = $store->id;
        $data['start_time'] = Carbon::now()->toDateTimeString();

        Settlement::create($data);

        Cache::forget('settlements_user_' . Auth::id());

        return redirect(route('settlement'))->with('success', 'New settlement created successfully!');
    }

    public function totalamount()
    {
        return view('addtotalamount');
    }

    public function posttotal(Request $request)
    {
        $store = auth()->user()->store;

        $user = auth()->user();

        $data = $request->validate([
            'total_amount' => 'nullable|numeric',
        ]);

        $latestSettlement = $user->settlements()->latest()->first();

        if (!$latestSettlement) {
            return redirect(route('settlement'))->with('error', 'No active shift found to end.');
        }

        $data['user_id'] = $user->id;
        $data['store_id'] = $store->id;
        $data['end_time'] = Carbon::now()->toDateTimeString();
        $latestSettlement->update($data);

        Cache::forget('settlements_user_' . Auth::id());

        return redirect(route('settlement'))->with('success', 'Shift ended successfully!');
    }

    public function show($id)
    {
        $settlement = Cache::remember("settlement_{$id}", now()->addMinutes(60), function () use ($id) {
            return Settlement::with('histoys')->find($id);
        });

        return view('showsettlement', compact('settlement'));
    }

    public function destroy($id)
    {
        Settlement::destroy($id);

        Cache::forget('settlements_user_' . Auth::id());

        return redirect(route('settlement'))->with('success', 'Settlement deleted successfully!');
    }
}

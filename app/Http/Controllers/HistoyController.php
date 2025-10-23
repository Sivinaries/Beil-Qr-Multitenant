<?php

namespace App\Http\Controllers;

use App\Models\Histoy;
use App\Exports\OrderExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class HistoyController extends Controller
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

        $cacheKey = 'histories_user_' . Auth::id();

        $history = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userStore) {
            return $userStore->histories;
        });

        return view('history', ['history' => $history]);
    }

    public function exportOrders(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
        ]);

        $month = $request->month;
        $history = Histoy::whereMonth('created_at', $month)->get();

        return Excel::download(new OrderExport($history, $month), 'history_' . $month . '.xlsx');
    }
}

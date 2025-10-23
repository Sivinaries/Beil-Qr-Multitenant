<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
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

        $cacheKey = 'expenses_user_' . Auth::id();

        $expenses = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userStore) {
            return $userStore->expenses;
        });
        
        return view('expense', compact('expenses'));
    }

    public function create()
    {
        return view('addexpense');
    }

    public function store(Request $request)
    {
        $userStore = auth()->user()->store;

        $data = $request->validate([
            'name' => 'required',
            'nominal' => 'required',
        ]);

        $data['store_id'] = $userStore->id;

        Expense::create($data);

        Cache::forget('expenses_user_' . Auth::id());

        return redirect(route('expense'))->with('success', 'Expense Sukses Dibuat !');
    }

    public function edit($id)
    {
        $expense = Expense::find($id);
        return view('editexpense', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $userStore = auth()->user()->store;

        $request->validate([
            'name' => 'required',
            'nominal' => 'required',
        ]);

        $data = $request->only(['name', 'nominal']);

        $data['store_id'] = $userStore->id;

        Expense::where('id', $id)->update($data);

        Cache::forget('expenses_user_' . Auth::id());

        return redirect(route('expense'))->with('success', 'Expense Sukses Diupdate !');
    }

    public function destroy($id)
    {
        Expense::destroy($id);

        Cache::forget('expenses_user_' . Auth::id());

        return redirect(route('expense'))->with('success', 'Expense Berhasil Dihapus !');
    }
}

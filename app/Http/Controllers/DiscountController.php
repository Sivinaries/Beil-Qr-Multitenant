<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DiscountController extends Controller
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

        $cacheKey = 'discounts_user_' . Auth::id();

        $discounts = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userStore) {
            return $userStore->discounts;
        });

        return view('discount', compact('discounts'));
    }

    public function create()
    {
        return view('adddiscount');
    }

    public function store(Request $request)
    {
        $userStore = auth()->user()->store;

        $data = $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);

        $data['store_id'] = $userStore->id;

        Discount::create($data);

        Cache::forget('discounts_user_' . Auth::id());

        return redirect(route('discount'))->with('success', 'Discount Sukses Dibuat !');
    }

    public function edit($id)
    {
        $discount = Discount::find($id);
        return view('editdiscount', compact('discount'));
    }

    public function update(Request $request, $id)
    {
        $userStore = auth()->user()->store;

        $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);

        $data = $request->only(['name', 'nominal']);

        $data['store_id'] = $userStore->id;

        Discount::where('id', $id)->update($data);

        Cache::forget('discounts_user_' . Auth::id());

        return redirect(route('discount'))->with('success', 'Discount Sukses Diupdate !');
    }

    public function destroy($id)
    {
        Discount::destroy($id);

        Cache::forget('discounts_user_' . Auth::id());

        return redirect(route('discount'))->with('success', 'Discount Berhasil Dihapus !');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Showcase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ShowcaseController extends Controller
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

        $cacheKey = 'showcases_user_' . Auth::id();

        $showcase = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userStore) {
            return $userStore->showcases;
        });
    
        return view('showcase', compact('showcase'));
    }
    
    public function create()
    {
        return view('addshowcase');
    }

    public function store(Request $request)
    {
        $userStore = auth()->user()->store;

        $data = $request->validate([
            'name' => 'required',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('img')) {
            $uploadedImage = $request->file('img');
            $imageName = $uploadedImage->getClientOriginalName();
            $imagePath = $uploadedImage->storeAs('img', $imageName, 'public');
            $data['img'] = 'img/' . $imageName;
        }

        $data['store_id'] = $userStore->id;

        Showcase::create($data);

        Cache::forget('showcases_user_' . Auth::id());

        return redirect(route('showcase'))->with('success', 'Showcase Sukses Dibuat !');
    }

    public function edit($id)
    {
        $showcase = Showcase::find($id);

        return view('editshowcase', compact('showcase'));
    }

    public function update(Request $request, $id)
    {
        $userStore = auth()->user()->store;

        $request->validate([
            'name' => 'required',
            'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->only(['name', 'img']);

        if ($request->hasFile('img')) {
            $uploadedImage = $request->file('img');
            $imageName = $uploadedImage->getClientOriginalName();
            $imagePath = $uploadedImage->storeAs('img', $imageName, 'public');
            $data['img'] = 'img/' . $imageName;
        }

        $data['store_id'] = $userStore->id;

        Showcase::where('id', $id)->update($data);

        Cache::forget('showcases_user_' . Auth::id());

        return redirect(route('showcase'))->with('success', 'Showcase Sukses Diupdate !');
    }

    public function destroy($id)
    {
        Showcase::destroy($id);

        Cache::forget('showcases_user_' . Auth::id());

        return redirect(route('showcase'))->with('success', 'Showcase Berhasil Dihapus !');
    }
}

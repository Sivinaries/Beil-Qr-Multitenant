<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\CartMenu;
use App\Models\Category;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
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
    
        $cacheKey = 'menus';
        
        $menu = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userStore) {
            return $userStore->menus; 
        });
    
        return view('product', compact('menu'));
    }
    
    public function create()
    {
        $userStore = Auth::user()->store;

        $category = $userStore->categories;

        return view('addproduct', compact('category'));
    }

    public function store(Request $request)
    {
        $userStore = auth()->user()->store;

        $data = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'category_id' => 'required',
        ]);

        $data['store_id'] = $userStore->id;

        if ($request->hasFile('img')) {
            $uploadedImage = $request->file('img');
            $imageName = $uploadedImage->getClientOriginalName();
            $imagePath = $uploadedImage->storeAs('img', $imageName, 'public');
            $data['img'] = 'img/' . $imageName;
        }

        Menu::create($data);

        Cache::forget('menus');

        return redirect(route('product'))->with('success', 'Product Sukses Dibuat !');
    }

    public function show($id)
    {
        $menu = Cache::remember("menu_{$id}", now()->addMinutes(60), function () use ($id) {
            return Menu::find($id);
        });
        
        $discount = Cache::remember('discounts', now()->addMinutes(60), function () {
            return Discount::all();
        });

        return view('showproduct', compact('menu', 'discount'));
    }

    public function edit($id)
    {
        $category = Category::all();

        $menu = Menu::find($id);

        return view('editproduct', compact('menu', 'category'));
    }

    public function update(Request $request, $id)
    {
        $userStore = auth()->user()->store;

        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'category_id' => 'required',
        ]);

        $menuData = $request->only(['name', 'price', 'img', 'description', 'category_id']);

        $menuData['store_id'] = $userStore->id;

        if ($request->hasFile('img')) {
            $uploadedImage = $request->file('img');
            $imageName = $uploadedImage->getClientOriginalName();
            $imagePath = $uploadedImage->storeAs('img', $imageName, 'public');
            $menuData['img'] = 'img/' . $imageName;
        }

        Menu::where('id', $id)->update($menuData);

        Cache::forget('menus');

        return redirect(route('product'))->with('success', 'Product Sukses Diupdate !');
    }

    public function destroy($id)
    {
        CartMenu::where('menu_id', $id)->delete();
        Menu::destroy($id);

        Cache::forget('menus');

        return redirect(route('product'))->with('success', 'Product Berhasil Dihapus !');
    }
}

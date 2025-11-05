<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
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

        $cacheKey = 'categories';

        $category = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userStore) {
            return $userStore->categories;
        });

        return view('category', compact('category'));
    }

    public function create()
    {
        return view('addcategory');
    }

    public function store(Request $request)
    {
        $userStore = auth()->user()->store;

        $data = $request->validate([
            'name' => 'required | unique:categories,name,NULL,id,store_id,' . $userStore->id,
        ]);

        $data['store_id'] = $userStore->id;

        Category::create($data);

        Cache::forget('categories');

        return redirect(route('category'))->with('success', 'Category successfully created!');
    }

    public function edit($id)
    {
        $category = Category::find($id);

        return view('editcategory', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $userStore = auth()->user()->store;

        $request->validate([
            'name' => 'required | unique:categories,name,NULL,id,store_id,' . $userStore->id,
        ]);

        $data = $request->only(['name']);

        $data['store_id'] = $userStore->id;

        Category::where('id', $id)->update($data);

        Cache::forget('categories');

        return redirect(route('category'))->with('success', 'Category successfully updated!');
    }

    public function destroy($id)
    {
        Category::destroy($id);

        Cache::forget('categories');

        return redirect(route('category'))->with('success', 'Category successfully deleted!');
    }
}

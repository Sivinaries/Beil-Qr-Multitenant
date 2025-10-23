<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\CartMenu;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
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

        $menus = $userStore->menus;

        return view('addcart', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'discount_id' => 'nullable|exists:discounts,id', // Discount can be nullable
        ]);

        $user = auth()->user();
        $cart = $user->carts()->latest()->first() ?? $user->carts()->create(['total_amount' => 0]);

        $menu = Menu::findOrFail($request->input('menu_id'));
        $quantity = $request->input('quantity');

        $subtotal = (float)$menu->price * (int)$quantity;

        $discount = null;

        if ($request->filled('discount_id')) {
            $discount = Discount::find($request->input('discount_id'));
            if ($discount) {
                $discountAmount = $subtotal * ($discount->percentage / 100);
                $subtotal -= $discountAmount; // Apply discount
            }
        }

        $existingCartMenu = CartMenu::where('cart_id', $cart->id)
            ->where('menu_id', $menu->id)
            ->where('notes', $request->input('notes'))
            ->where('discount_id', $discount ? $discount->id : null)
            ->first();

        if ($existingCartMenu) {
            $existingCartMenu->quantity += $quantity;
            $existingCartMenu->subtotal += $subtotal;
            $existingCartMenu->save();
        } else {
            CartMenu::create([
                'cart_id' => $cart->id,
                'menu_id' => $menu->id,
                'quantity' => $quantity,
                'notes' => $request->input('notes'),
                'subtotal' => $subtotal,
                'discount_id' => $discount ? $discount->id : null, // Assign discount ID if applicable
            ]);
        }

        $cart->update(['total_amount' => $cart->total_amount + $subtotal]);

        return redirect(route('addorder'));
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $cart = $user->carts()->latest()->first();

        $cartMenu = CartMenu::findOrFail($id);

        $subtotal = $cartMenu->subtotal;

        $cartMenu->discount_id;

        $cartMenu->delete();

        $cart->update(['total_amount' => $cart->total_amount - $subtotal]);

        return redirect()->route('addorder');
    }
}

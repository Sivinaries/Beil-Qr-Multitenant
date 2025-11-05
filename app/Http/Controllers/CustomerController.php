<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Ramsey\Uuid\Uuid;
use App\Models\CartMenu;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CustomerController extends Controller
{
    public function home()
    {
        if (!Auth::guard('chair')->check()) {
            return redirect('/');
        }

        $chairStore = Auth::guard('chair')->user()->store;

        $storeKey = 'stores';
        $showKey = 'showcases';
        $menusKey = 'menus'; // Cache key for menus

        $store = Cache::remember($storeKey, now()->addMinutes(60), function () use ($chairStore) {
            return $chairStore;
        });

        $showcase = Cache::remember($showKey, now()->addMinutes(60), function () use ($chairStore) {
            return $chairStore->showcases()->select('id', 'img')->get()->toArray();
        });

        $menus = Cache::remember($menusKey, now()->addMinutes(60), function () use ($chairStore) {
            return $chairStore->menus()->select('id', 'name', 'price', 'img')->paginate(10);
        });

        return view('user.home', compact('store', 'menus', 'showcase'));
    }

    public function product()
    {

        $chairStore = Auth::guard('chair')->user()->store;

        $categoryKey = 'categories';

        $category = Cache::remember($categoryKey, now()->addMinutes(60), function () use ($chairStore) {
            return $chairStore->categories()->with('menus')->get();
        });

        $chair = auth()->user();

        $cart = $chair->carts()->latest()->first();

        if (!$cart) {
            $cart = $chair->carts()->create([
                'store_id' => $chairStore->id,
            ]);
        }

        return view('user.product', compact('category', 'cart'));
    }

    public function cart()
    {
        $chairStore = Auth::guard('chair')->user()->store;

        $chair = auth()->user();

        $cart = $chair->carts()->with(['cartMenus.menu'])->latest()->first();

        if (!$cart) {
            $cart = $chair->carts()->create([
                'store_id' => $chairStore->id,
            ]);
        }

        return view('user.cart', compact('cart'));
    }

    public function show($id)
    {
        $chairStore = Auth::guard('chair')->user()->store;

        $menu = Cache::remember("menu_{$id}", now()->addMinutes(60), function () use ($id) {
            return Menu::find($id);
        });

        $discount = Cache::remember('discounts', now()->addMinutes(60), function () use ($chairStore) {
            return $chairStore->discounts;
        });

        return view('user.show', compact('menu', 'discount'));
    }

    public function postcart(Request $request)
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

        return redirect(route('user-product'));
    }

    public function removecart($id)
    {
        $chair = auth()->user();

        $cart = $chair->carts()->latest()->first();

        $cartMenu = CartMenu::findOrFail($id);

        $subtotal = $cartMenu->subtotal;

        $cartMenu->discount_id;

        $cartMenu->delete();

        $cart->update(['total_amount' => $cart->total_amount - $subtotal]);


        return redirect()->route('user-cart');
    }

    public function serve()
    {
        return view('user.serve');
    }

    public function payment(Request $request)
    {
        $chair = auth()->user();

        $cart = $chair->carts()->with('cartMenus.menu')->latest()->first();

        $order = Order::where('cart_id', $cart->id)->first();

        return view('user.payment', compact('order', 'cart'));
    }

    public function postdineIn()
    {
        $chairStore = Auth::guard('chair')->user()->store;

        $chair = auth()->user();

        $cart = $chair->carts()->with('user')->latest()->first();

        if (!$cart) {
            return redirect()->back()->with('error', 'No cart found.');
        }

        $order = Order::where('cart_id', $cart->id)->first();

        if ($order) {
            $order->layanan = 'dineIn';
            $order->save();
        } else {
            $order = new Order();
            $order->cart_id = $cart->id;
            $order->store_id = $chairStore->id;
            $order->layanan = 'dineIn';
            $order->save();
        }

        return redirect()->route('user-payment');
    }

    public function posttakeAway(Request $request)
    {
        $chairStore = Auth::guard('chair')->user()->store;

        $chair = auth()->user();

        $cart = $chair->carts()->with('user')->latest()->first();

        if (!$cart) {
            return redirect()->back()->with('error', 'No cart found.');
        }

        $order = Order::where('cart_id', $cart->id)->first();

        if ($order) {
            $order->layanan = 'takeAway';
            $order->save();
        } else {
            $order = new Order();
            $order->cart_id = $cart->id;
            $order->store_id = $chairStore->id;
            $order->layanan = 'takeAway';
            $order->save();
        }

        return redirect()->route('user-payment');
    }

    public function postorder(Request $request)
    {
        $chairStore = Auth::guard('chair')->user()->store->id;

        $chair = auth()->user();

        $request->validate([
            'no_telpon' => 'required|string|max:15',
            'atas_nama' => 'required|string|max:255',
            'alamat' => 'nullable',
            'ongkir' => 'nullable',
        ]);

        $cart = $chair->carts()->with('cartMenus.menu')->latest()->first();

        if (!$cart || $cart->cartMenus->isEmpty()) {
            return redirect()->route('user-cart')->with('error', 'Your cart is empty.');
        }

        $order = Order::where('cart_id', $cart->id)->first();

        if (!$order) {
            return redirect()->route('user-home');
        }

        $orderId = 'ORDER-' . strtoupper(substr(Uuid::uuid4()->toString(), 0, 8));

        $order->update([
            'store_id' => $chairStore,
            'no_order' => $orderId,
            'atas_nama' => $request->atas_nama,
            'no_telpon' => $request->no_telpon,
        ]);

        $cart->update(['total_amount' => $cart->total_amount + ($order->ongkir ?? 0)]);

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = true;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderId,
                'gross_amount' => $order->cart->total_amount,
            )
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $chair->carts()->create();

        return view('user.checkout', compact('order', 'snapToken'));
    }

    public function antrian()
    {
        $orders = Order::with(['cart.user', 'cart.cartMenus.menu'])->get();
        $statuses = [];

        foreach ($orders as $order) {
            try {
                if ($order->status === 'settlement' && $order->payment_type === 'cash') {
                    $statuses[$order->no_order] = (object) [
                        'status' => $order->status,
                        'bg_color' => 'text-white text-center bg-green-500 w-fit rounded-xl'
                    ];
                    continue; // Skip further processing for this order
                }

                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = true;

                $status = \Midtrans\Transaction::status($order->no_order);

                $order->update([
                    'status' => $status->transaction_status,
                    'payment_type' => $status->payment_type ?? null,
                ]);

                if ($status->transaction_status === 'expire') {
                    $order->delete();
                    continue;
                }

                $statuses[$order->no_order] = (object) [
                    'status' => $status->transaction_status,
                    'bg_color' => $status->transaction_status === 'settlement' ? 'text-white text-center bg-green-500 w-fit rounded-xl' : 'text-white text-center bg-red-500 w-fit rounded-xl'
                ];
            } catch (\Exception $e) {
                $statuses[$order->no_order] = (object) [
                    'status' => 'Error: ' . $e->getMessage(),
                    'bg_color' => 'bg-red-500 w-fit text-white text-center rounded-xl'
                ];
            }
        }

        return view('user.antrian', compact('orders', 'statuses'));
    }

    public function game()
    {
        return view('user.game');
    }

    public function akun()
    {
        $chairId = Auth::guard('chair')->user()->id;

        $cacheKey = 'chairs';

        $chair = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($chairId) {
            return Auth::guard('chair')->user();
        });

        return view('user.akun', compact('chair'));
    }
}
